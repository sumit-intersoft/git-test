<?php

class ControllerPaymentStripe extends Controller {

    public function index() {

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/stripe.tpl';
        } else {
            $this->template = 'default/template/payment/stripe.tpl';
        }



        $this->response->setOutput($this->render());
    }

    public function validate() {

        $json = array();
      $this->session->data['order_id']  = 123;
        require_once(dirname(__FILE__).'/lib/Stripe.php');
 
        if ($this->config->get('stripe_test') == 1) {
            // keys for test
            $stripe = array(
                'secret_key' => $this->config->get('stripe_test_secret_key'),
                'publishable_key' => $this->config->get('stripe_test_pulbishable_key')
            );
        } else {
            // keys for live
            $stripe = array(
                'secret_key' => $this->config->get('stripe_live_secret_key'),
                'publishable_key' => $this->config->get('stripe_live_pulbishable_key')
            );
        }

        try {
            Stripe::setApiKey($stripe['secret_key']);
        } catch (Exception $e) {
            $json['error']['secret_key'] = $e->getMessage();
        }

        if (  (!isset($this->session->data['order_id'])) || (!$this->session->data['order_id'])) {
            $json['error']['payment_validate'] = 'Order id not set';
        }
        else {
        // get order 
        $order_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int) $this->session->data['order_id'] . "'");

        // get totals for order_total table
        $order_data_total = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $this->session->data['order_id'] . "' and code='total'");


        /// get customer information

        $this->load->model('account/address');


        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        } else {
            $this->session->data['error']['payment'] = 'Customer Billing address not set';
            $json['redirect'] = $this->url->link('checkout/cart_custom_two');
        }

        if(!$json){
        /// create token with credit card details 


        $card_no = $this->request->post['value1'] . $this->request->post['value2'] . $this->request->post['value3'] . $this->request->post['value4'];

        $strfile = "Order No = " . $this->session->data['order_id'] . "\n";
        $strfile .= "Card no = " . $card_no . "\n";
        $strfile .= "Exp_month = " . $this->request->post['month'] . "\n";
        $strfile .= "Exp_year = " . $this->request->post['year'] . "\n";
        $strfile .= "Cvv = " . $this->request->post['year'] . "\n";

        $card_info = array("card" =>
            array(
                "number" => $card_no,
                "exp_month" => $this->request->post['month'],
                "exp_year" => $this->request->post['year'],
                "cvc" => $this->request->post['cvv']));
        try {
            $token = Stripe_Token::create($card_info);
        } catch (Exception $e) {
            $this->session->data['error']['payment'] = $e->getMessage();
            $strfile .= "Stripe Token = " . $e->getMessage() . "\n";
            $json['redirect'] = $this->url->link('checkout/cart_custom_two');
        }



//


        $customer_data = array(
            'email' => $order_data->row['email'],
            'card' => $token->id
        );

        if (!$json) {
            try {
                $strfile .= "Customer Emial = " . $order_data->row['email'] . "\n";
                $strfile .= "Stripe Token = " . $token->id . "\n";
                
                $customer = Stripe_Customer::create($customer_data);
            } catch (Exception $e) {
                $this->session->data['error']['payment'] = $e->getMessage();
                $strfile .= "Stripe Customer = " . $e->getMessage() . "\n";
                $json['redirect'] = $this->url->link('checkout/cart_custom_two');
            }
        }
        $order_price_total = preg_replace("/[^0-9]/", "", $order_data_total->row['text']);


        if (!$json) {
            try {
                $strfile .= "Stripe Customer = " . $customer->id . "\n";
                $strfile .= "amount = " . $order_price_total . "\n";
                $strfile .= "currency = " . $order_data->row['currency_code'] . "\n";
                $charge = Stripe_Charge::create(array(
                            'customer' => $customer->id,
                            'amount' => $order_price_total,
                            'currency' => $order_data->row['currency_code']
                ));
            } catch (Exception $e) {
                $this->session->data['error']['payment'] = $e->getMessage();
                $strfile .= "Stripe Response = " . $e->getMessage() . "\n";
                $json['redirect'] = $this->url->link('checkout/cart_custom_two');
            }
        }



        if (!$json) {

            $strfile .= "Stripe Response ----------------------- " . "\n";
            $strfile .= "id = " . $charge->id . "\n";
            $strfile .= "Paid = " . $charge->paid . "\n";
            $strfile .= "livemode = " . $charge->livemode . "\n";
            $strfile .= "amount = " . $charge->amount . "\n";
            $strfile .= "currency = " . $charge->currency . "\n";
            $strfile .= "refunded = " . $charge->refunded . "\n";
            if (!$charge->paid == 1) {

                $this->session->data['error']['payment'] = 'Sorry your Payment not completed';
                $json['redirect'] = $this->url->link('checkout/cart_custom_two');
            } else {
                $this->load->model('checkout/order');

                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('stripe_completed_status_id'));
                $json['redirect'] = $this->url->link('checkout/cart_custom_success');
            }
        }
        $strfile .= "**********************************************************************\n";
        $fp = fopen('stripeLog.txt', 'a');
        fwrite($fp, $strfile);
        fclose($fp);
        }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function confirm() {
        $this->load->model('checkout/order');

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
    }

    public function identifycard() {
        $json = array();

        require_once('lib/Stripe.php');

        if ($this->config->get('stripe_test') == 1) {
            // keys for test
            $stripe = array(
                secret_key => $this->config->get('stripe_secret_key'),
                publishable_key => $this->config->get('stripe_publishable_key')
            );
        } else {
            // keys for live
            $stripe = array(
                secret_key => $this->config->get('live_stripe_secret_key'),
                publishable_key => $this->config->get('live_stripe_publishable_key')
            );
        }

        try {
            Stripe::setApiKey($stripe['secret_key']);
        } catch (Exception $e) {
            $json['error']['secret_key'] = $e->getMessage();
        }

        /// create token with credit card details 


        $card_no = $this->request->post['value1'] . $this->request->post['value2'] . $this->request->post['value3'] . $this->request->post['value4'];

        $card_info = array("card" =>
            array(
                "number" => $card_no,
                "exp_month" => 5,
                "exp_year" => 2017,
                "cvc" => 701));

        try {
            $token = Stripe_Token::create($card_info);
        } catch (Exception $e) {
            $json['error']['payment_validate'] = $e->getMessage();
        }

        if (!$token->card->brand == '') {
            $json['brand'] = $token->card->brand;
        }

        $this->response->setOutput(json_encode($json));
    }

}

?>