<?php

class ControllerPaymentStripe extends Controller {

    public function index() {


        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['text_loading'] = $this->language->get('text_loading');

        $data['continue'] = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/stripe.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/stripe.tpl', $data);
        }

        $this->response->setOutput($this->render());
        
    }

    public function payment_form() {

        $this->load->language('payment/authorizenet_aim');

        $data['text_credit_card'] = $this->language->get('text_credit_card');
        $data['text_wait'] = $this->language->get('text_wait');

        $data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
        $data['entry_text_testmode'] = $this->language->get('text_testmode');
        
        $data['testmode'] = $this->config->get('stripe_test');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
            $data['months'][] = array(
                'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
                'value' => sprintf('%02d', $i)
            );
        }

        $today = getdate();

        $data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $data['year_expire'][] = array(
                'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_payment_form.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/stripe_payment_form.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/stripe_payment_form.tpl', $data);
        }
    }
    
    protected function setup_env() {
        
        require_once(dirname(__FILE__) . '/stripe/lib/Stripe.php');

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

       return $stripe;
        
    }

    public function confirm() {

        $json = array();
        
        require_once(dirname(__FILE__) . '/stripe/lib/Stripe.php');

        $settings = $this->config->get('customcheckout_status');
        if(isset($settings) && $settings == 1){
           $redirect_url = $this->url->link('checkout/view_checkout');
        } else {
           $redirect_url = $this->url->link('checkout/cart') ;
        }
               
        $setup_env= $this->validate();
        
        if(isset($setup_env['error']) &&  $setup_env['error']) {
            $this->session->data['error']['payment'] = $e->getMessage();
            $json['redirect'] = $redirect_url;
        } else {
           $stripe = $setup_env['stripe'];
           $token = $setup_env['token'];
        }
        
        if ((!isset($this->session->data['order_id'])) || (!$this->session->data['order_id'])) {
            $json['error']['payment_validate'] = 'Order id not set';
            $json['redirect'] = $redirect_url;
        } else {

            $this->load->model('checkout/order');

            $order_info = $this->model_checkout_order->getOrder((int) $this->session->data['order_id']);
            
            if($order_info){
                $this->load->model('account/address');


                if ($this->customer->isLogged() && isset($this->session->data['payment_address'])) {
                    $payment_address = $this->session->data['payment_address'];
                }
                
                else {

                    $this->session->data['error']['payment'] = 'Customer Billing address not set';
                    $json['redirect'] = $redirect_url;
                }

                if (!$json) {
                    
                    $customer_data = array(
                        'email' => $order_info['email'],
                        'metadata' => array('firstname'=> $order_info['payment_firstname'],'lastname'=> $order_info['payment_lastname']),
                        'card' => $token->id
                    );

                    
                    try {
                        $customer = Stripe_Customer::create($customer_data);
                    } catch (Exception $e) {
                        $this->session->data['error']['payment'] = $e->getMessage();
                        $json['redirect'] = $redirect_url;
                    }
                    

                    $order_price_total = intval($this->currency->format($order_info['total'], $order_info['currency_code'], false, false) * 100);

                    if (!$json) {
                        try {
                            $charge = Stripe_Charge::create(array(
                                        'customer' => $customer->id,
                                        'amount' => $order_price_total,
                                        'currency' => $order_info['currency_code'],
                                        'metadata' => array("order_id" => (int) $this->session->data['order_id'],'firstname'=> $order_info['payment_firstname'],'lastname'=> $order_info['payment_lastname'])
                            ));
                             $this->log->write('STRIPE :: IPN RESPONSE: ' . $charge);
                        } catch (Exception $e) {
                            $this->session->data['error']['payment'] = $e->getMessage();
                            $json['redirect'] = $redirect_url;
                        }
                    }

                    if (!$json) {
                        $order_status_id = $this->config->get('config_order_status_id');
                        
                        if ($charge->paid == 1  && ($charge->amount == $order_price_total)) {
                            $order_status_id =  $this->config->get('stripe_completed_status_id');
                        } else {
                             $order_status_id =  $this->config->get('stripe_pending_status_id');
                        }
                        
                        $this->model_checkout_order->addOrderHistory((int) $this->session->data['order_id'], $order_status_id);
                        $json['redirect'] = $this->url->link('checkout/success');
                    }
                }
            } else {
                $json['redirect'] = $redirect_url;
            }
        }
        
        $this->response->setOutput(json_encode($json));
    }

    public function validate() {
        
        $json = array();

        $stripe= $this->setup_env();
        
        try {
            Stripe::setApiKey($stripe['secret_key']);
        } catch (Exception $e) {
            $json['error'] = $e->getMessage();
        }

        
        if(!$json) {
            /// create token with credit card details 

            $card_no = $this->request->post['value1'] . $this->request->post['value2'] . $this->request->post['value3'] . $this->request->post['value4'];
            
            $card_info = array("card" =>
                                array(
                                    "number" => $card_no,
                                    "exp_month" => (isset($this->request->post['cc_expire_date_month']) ?  intval($this->request->post['cc_expire_date_month'])  : 5),
                                    "exp_year" => (isset($this->request->post['cc_expire_date_year']) ? intval($this->request->post['cc_expire_date_year']) : 2017),
                                    "cvc" => (isset( $this->request->post['cc_cvv2']) ? $this->request->post['cc_cvv2'] : 701)
                                    )
                                );

            try {
                $token = Stripe_Token::create($card_info);
            } catch (Exception $e) {
                $json['error'] = $e->getMessage();
            }

            if(!$json) {
                    $json['token'] = $token;    //$token->card->brand
                    $json['stripe']= $stripe;
            }
        }
        
        return $json;
    }
}

?>