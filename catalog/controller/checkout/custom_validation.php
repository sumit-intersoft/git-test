<?php

class ControllerCheckoutCustomValidation extends Controller {

    private $error = array();

    public function index() {
        
    }

    public function signUpValidate() {
        $this->language->load('checkout/checkout');

        $this->load->model('account/customer');
        
        unset($this->session->data['register_account']);
        $json = array();
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts())) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }
        
        if (!$this->customer->isLogged()) {
            if (!$json) {
                if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
                    $json['error']['email'] = $this->language->get('error_email');
                }

                if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                    $json['error']['email'] = $this->language->get('error_exists');
                }
                if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                    $json['error']['password'] = $this->language->get('error_password');
                }

                if ($this->request->post['confirm'] != $this->request->post['password']) {
                    $json['error']['confirm'] = $this->language->get('error_confirm');
                }
            }
            if (!$json) {
                $this->session->data['register_account']['email'] = $this->request->post['email'];
                $this->session->data['register_account']['password'] = $this->request->post['password'];
                $this->session->data['register_account']['confirm'] = $this->request->post['confirm'];
            }
        
        } 
        $this->response->setOutput(json_encode($json));
    }

    public function loginValidate() {
        $this->language->load('checkout/checkout');

        $json = array();

        if ($this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/cart_custom', '', 'SSL');
        }

        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart_custom');
        }

        if (!$json) {
            if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
                $json['error']['email'] = $this->language->get('error_email');
            }
            if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                $json['error']['password'] = $this->language->get('error_password');
            }
        }

        if (!$json) { 
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                
                $json['error']['warning'] = $this->language->get('error_login');
            }

            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            if ($customer_info && !$customer_info['approved']) {
                $json['error']['warning'] = $this->language->get('error_approved');
            }
        }
        if (!$json) {
            unset($this->session->data['guest']);

            // Default Addresses
            $this->load->model('account/address');

            $address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

            if ($address_info) {
                if ($this->config->get('config_tax_customer') == 'shipping') {
                    $this->session->data['shipping_country_id'] = $address_info['country_id'];
                    $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                    $this->session->data['shipping_postcode'] = $address_info['postcode'];
                }

                if ($this->config->get('config_tax_customer') == 'payment') {
                    $this->session->data['payment_country_id'] = $address_info['country_id'];
                    $this->session->data['payment_zone_id'] = $address_info['zone_id'];
                }
            } else {
                unset($this->session->data['shipping_country_id']);
                unset($this->session->data['shipping_zone_id']);
                unset($this->session->data['shipping_postcode']);
                unset($this->session->data['payment_country_id']);
                unset($this->session->data['payment_zone_id']);
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function registerValidate() {
          
        $this->language->load('checkout/checkout');
        $this->load->model('account/customer');
        $json = array();
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart_custom');
        }
        if (!$this->customer->isLogged()) {
// Validate minimum quantity requirments.			
            $products = $this->cart->getProducts();

            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $json['redirect'] = $this->url->link('checkout/cart_custom');

                    break;
                }
            }
            if (!$json) {
                if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
                    $json['error']['firstname'] = $this->language->get('error_firstname');
                }

                if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
                    $json['error']['lastname'] = $this->language->get('error_lastname');
                }
                if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
                    $json['error']['email'] = $this->language->get('error_email');
                }
                
                if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
		}

                if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                    $json['error']['warning'] = $this->language->get('error_exists');
                }

                // Customer Group
                $this->load->model('account/customer_group');

                if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                    $customer_group_id = $this->request->post['customer_group_id'];
                } else {
                    $this->request->post['customer_group_id'] = $customer_group_id = $this->config->get('config_customer_group_id');
                }

                $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

                if ($customer_group) {
                    // Company ID
                    if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
                        $json['error']['company_id'] = $this->language->get('error_company_id');
                    }

                    // Tax ID
                    if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
                        $json['error']['tax_id'] = $this->language->get('error_tax_id');
                    }
                }

                if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
                    $json['error']['address_1'] = $this->language->get('error_address_1');
                }
                if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
                    $json['error']['city'] = $this->language->get('error_city');
                }

                $this->load->model('localisation/country');

                $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

                if ($country_info) {
                    if ((utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
                        $json['error']['postcode'] = $this->language->get('error_postcode');
                }

                    // VAT Validation
                    $this->load->helper('vat');

                    if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
                        $json['error']['tax_id'] = $this->language->get('error_vat');
                    }
                }

                if ($this->request->post['country_id'] == '') {
                    $json['error']['country'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                    $json['error']['zone'] = $this->language->get('error_zone');
                }

                if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                    $json['error']['password'] = $this->language->get('error_password');
                }
//            if ($this->request->post['confirm'] != $this->request->post['password']) {
//                $json['error']['confirm'] = $this->language->get('error_confirm');
//            }

                if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
                    $json['error']['shipping_firstname'] = $this->language->get('error_firstname');
                }

                if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
                    $json['error']['shipping_lastname'] = $this->language->get('error_lastname');
                }

                if ($this->request->post['shipping_country_id'] == '') {
                    $json['error']['shipping_country'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['shipping_zone_id']) || $this->request->post['shipping_zone_id'] == '') {
                    $json['error']['shipping_zone'] = $this->language->get('error_zone');
                }

                if ((utf8_strlen($this->request->post['shipping_postcode']) < 2) || (utf8_strlen($this->request->post['shipping_postcode']) > 10)) {
                    $json['error']['shipping_postcode'] = $this->language->get('error_postcode');
                }
                if ((utf8_strlen($this->request->post['shipping_city']) < 2) || (utf8_strlen($this->request->post['shipping_city']) > 128)) {
                    $json['error']['shipping_city'] = $this->language->get('error_city');
                }
                if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
                    $json['error']['shipping_address_1'] = $this->language->get('error_address_1');
                }
            }

            // shipping method
            
            if (!$json) {
                $this->language->load('checkout/checkout');

                $this->load->model('account/address');

                if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
                    $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
                } else {
                    $shipping_address = array('country_id' => $this->request->post['shipping_country_id'], 'zone_id' => $this->request->post['shipping_zone_id']);
                }

                if (empty($shipping_address)) {
                    $json['error']['warning'] = 'Shipping information not set';
                }

                if (!$json) { 
                    if (!isset($this->request->post['shipping_method'])) {
                        $json['error']['warning'] = $this->language->get('error_shipping');
                    } else {
                        $shipping = explode('.', $this->request->post['shipping_method']);

                        if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                            $json['error']['warning'] = $this->language->get('error_shipping');
                        }
                    }
                  
                    if (!$json) {
                        $shipping = explode('.', $this->request->post['shipping_method']);

                        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                       
                    }
                }
            }
            
             // payment method validations
            
        if(!$json){
        
            if (!isset($this->request->post['payment_method'])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			}	

			

			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

				
			}	
        }
       
         if(!$json){
            
            if(!$this->request->post['custom_payment_method'])
                $json['error']['custom_payment_method'] ='No payment method selected';
            else
                $this->session->data['custom_payment_method'] = $this->request->post['custom_payment_method'];
            
            if($this->request->post['payment_method'] == 'cod')
            {
                 
                $card_no=$this->request->post['card_no'];

                if($this->request->post['card_type']){
                     $card_detail['card_type'] = $this->request->post['card_type'];
                }
                    if ((utf8_strlen($card_no) < 13) || (utf8_strlen($card_no) > 32)) {
                        $json['error']['card'] ='Card number is Invalid!';
                    } elseif(!($this->creditcardValidate($card_detail['card_type'],$card_no, true))) {
                         $json['error']['card'] ='Card number is Invalid!';
                    }
                    else {
                        $card_detail['card_no'] = $card_no;
                    }
                    if ((utf8_strlen($this->request->post['cvv']) < 3) || (utf8_strlen($this->request->post['cvv']) > 4)) {
                        $json['error']['cvv'] = 'Cvv number is Invalid!';
                    } else {
                        $card_detail['cvv'] = $this->request->post['cvv'];
                    }
                    if ((utf8_strlen($this->request->post['card_expiry_month']) < 1)) {
                       $json['error']['validity'] = 'Validity required';
                    } else {
                       $card_detail['card_expiry_month'] = $this->request->post['card_expiry_month'];
                    }
                    if ((utf8_strlen($this->request->post['card_expiry_year']) < 1)) {
                       $json['error']['validity'] = 'Validity required';
                    } else {
                       $card_detail['card_expiry_year'] = $this->request->post['card_expiry_year'];
                    }
                  
                    if( !($json['error']['validity']) )
                    {
                      if((strtotime(date($this->request->post['card_expiry_year']."/".$this->request->post['card_expiry_month']."/01"))) < (strtotime(date('Y/m/01')))) {
                        $json['error']['validity'] = 'Credit Card is  expired!';
                        }                    
                    }
                    $this->session->data['card_detail'] = $card_detail;
            }    
                
                if (isset($this->request->post['comment'])) {
				$comment = $this->request->post['comment'];				
			} elseif (isset($this->session->data['comment'])) {
				$comment = $this->session->data['comment'];
			} else {
				$comment = '';
			}
                $this->session->data['comment'] = $comment;
                
         }
            
            if (!$json) {
                $this->model_account_customer->addCustomer($this->request->post,true);

                $this->session->data['account'] = 'register';

                unset($this->session->data['guest']);
               

                $this->customer->login($this->request->post['email'], $this->request->post['password']);
                $this->load->model('account/address');
                $this->session->data['payment_address_id'] = $this->customer->getAddressId();
                $shipping_address['customer_id'] = $this->customer->getId();
                $shipping_address['firstname'] = $this->request->post['shipping_firstname'];
                $shipping_address['lastname'] = $this->request->post['shipping_lastname'];
                $shipping_address['company'] = '';
                $shipping_address['address_1'] = $this->request->post['shipping_address_1'];
                $shipping_address['address_2'] = $this->request->post['shipping_address_2'];
                $shipping_address['city'] = $this->request->post['shipping_city'];
                $shipping_address['postcode'] = $this->request->post['shipping_postcode'];
                $shipping_address['country_id'] = $this->request->post['shipping_country_id'];
                $shipping_address['zone_id'] = $this->request->post['shipping_zone_id'];

                if($this->request->post['is_shipping'])    //add new address only when the address is different from billing
                    $this->session->data['shipping_address_id'] = $this->session->data['payment_address_id'];
                else
                   $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($shipping_address);
            }
        } else {

            /* if user is logged in 
              if user has new address
             */


            if ($this->request->post['payment_address'] == 'new') {
                if (!$json) {
                    if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
                        $json['error']['firstname'] = $this->language->get('error_firstname');
                    }

                    if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
                        $json['error']['lastname'] = $this->language->get('error_lastname');
                    }


                    if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
                        $json['error']['address_1'] = $this->language->get('error_address_1');
                    }
                    if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
                        $json['error']['city'] = $this->language->get('error_city');
                    }

                    $this->load->model('localisation/country');

                    $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

                    if ($country_info) {
                        if ((utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
                            $json['error']['postcode'] = $this->language->get('error_postcode');
                        }

                        // VAT Validation
                        $this->load->helper('vat');

                        if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
                            $json['error']['tax_id'] = $this->language->get('error_vat');
                        }
                    }

                    if ($this->request->post['country_id'] == '') {
                        $json['error']['country'] = $this->language->get('error_country');
                    }

                    if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                        $json['error']['zone'] = $this->language->get('error_zone');
                    }
                }
            }
            if ($this->request->post['shipping_address'] == 'new') {
                if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
                    $json['error']['shipping_firstname'] = $this->language->get('error_firstname');
                }

                if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
                    $json['error']['shipping_lastname'] = $this->language->get('error_lastname');
                }

                if ($this->request->post['shipping_country_id'] == '') {
                    $json['error']['shipping_country'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['shipping_zone_id']) || $this->request->post['shipping_zone_id'] == '') {
                    $json['error']['shipping_zone'] = $this->language->get('error_zone');
                }

                if ((utf8_strlen($this->request->post['shipping_postcode']) < 2) || (utf8_strlen($this->request->post['shipping_postcode']) > 10)) {
                    $json['error']['shipping_postcode'] = $this->language->get('error_postcode');
                }
                if ((utf8_strlen($this->request->post['shipping_city']) < 2) || (utf8_strlen($this->request->post['shipping_city']) > 128)) {
                    $json['error']['shipping_city'] = $this->language->get('error_city');
                }
                if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
                    $json['error']['shipping_address_1'] = $this->language->get('error_address_1');
                }
            }
            if (!$json) {

                $this->language->load('checkout/checkout');

                $this->load->model('account/address');


                $shipping_address = array('country_id' => $this->request->post['shipping_country_id'], 'zone_id' => $this->request->post['shipping_zone_id']);


                if (empty($shipping_address)) {
                    $json['error']['warning'] = 'Shipping information not set';
                }

                if (!$json) {
                    if (!isset($this->request->post['shipping_method'])) {
                        $json['error']['warning'] = $this->language->get('error_shipping');
                    } else {
                        $shipping = explode('.', $this->request->post['shipping_method']);

                        if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                            $json['error']['warning'] = $this->language->get('error_shipping');
                        }
                    }

                    if (!$json) {
                     
                        $shipping = explode('.', $this->request->post['shipping_method']);
                       

                         $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                         $this->session->data['shipping_method_selected'] = $this->request->post['shipping_method'];
                        
                    }
                }
            }

            
             // payment method validations
         //echo '<pre>'; print_r($this->session->data['payment_methods']); echo '</pre>';
            
        if(!$json){
        
            if (!isset($this->request->post['payment_method'])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			}	

			

			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

				
			}	
        }
       
         if(!$json){
            if(!$this->request->post['custom_payment_method'])
                $json['error']['custom_payment_method'] ='No payment method selected';
            else
                $this->session->data['custom_payment_method'] = $this->request->post['custom_payment_method'];
             
            if($this->request->post['payment_method'] == 'cod')
            {
                
                $card_no=$this->request->post['card_no'];
                if($this->request->post['card_type']){
                    $card_detail['card_type'] = $this->request->post['card_type'];
                }
                if ((utf8_strlen($card_no) < 13) || (utf8_strlen($card_no) > 32)) {
                       $json['error']['card'] ='Card number is Invalid!';
                   }  elseif(!($this->creditcardValidate($card_detail['card_type'],$card_no, true))) {
                         $json['error']['card'] ='Card number is Invalid!';
                    }   else {
                       $card_detail['card_no'] = $card_no;
                   }
                   if ((utf8_strlen($this->request->post['cvv']) < 3) || (utf8_strlen($this->request->post['cvv']) > 4)) {
                       $json['error']['cvv'] = 'Cvv number is Invalid!';
                   } else {
                       $card_detail['cvv'] = $this->request->post['cvv'];
                   }
                   if ((utf8_strlen($this->request->post['card_expiry_month']) < 1)) {
                       $json['error']['validity'] = 'Validity required';
                   } else {
                       $card_detail['card_expiry_month'] = $this->request->post['card_expiry_month'];
                   }
                    if ((utf8_strlen($this->request->post['card_expiry_year']) < 1)) {
                       $json['error']['validity'] = 'Validity required';
                   } else {
                       $card_detail['card_expiry_year'] = $this->request->post['card_expiry_year'];
                   }
                  
                    if( !($json['error']['validity']) )
                    {
                      if((strtotime(date($this->request->post['card_expiry_year']."/".$this->request->post['card_expiry_month']."/01"))) < (strtotime(date('Y/m/01')))) {
                        $json['error']['validity'] = 'Credit Card is  expired!';
                        }                    
                    }  
                    $this->session->data['card_detail'] = $card_detail;
            }    
                 if (isset($this->request->post['comment'])) {
				$comment = $this->request->post['comment'];				
			} elseif (isset($this->session->data['comment'])) {
				$comment = $this->session->data['comment'];
			} else {
				$comment = '';
			}
                $this->session->data['comment'] = $comment;
                
         }
            
            if (!$json) {
                if ($this->request->post['shipping_address'] == 'new') {
                    $this->load->model('account/address');
                    $shipping_address['customer_id'] = $this->customer->getId();
                    $shipping_address['firstname'] = $this->request->post['shipping_firstname'];
                    $shipping_address['lastname'] = $this->request->post['shipping_lastname'];
                    $shipping_address['company'] = '';
                    $shipping_address['address_1'] = $this->request->post['shipping_address_1'];
                    $shipping_address['address_2'] = $this->request->post['shipping_address_2'];
                    $shipping_address['city'] = $this->request->post['shipping_city'];
                    $shipping_address['postcode'] = $this->request->post['shipping_postcode'];
                    $shipping_address['country_id'] = $this->request->post['shipping_country_id'];
                    $shipping_address['zone_id'] = $this->request->post['shipping_zone_id'];

                   $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($shipping_address);
                }
                if ($this->request->post['payment_address'] == 'new') {
                    $payment_address['customer_id'] = $this->customer->getId();
                    $payment_address['firstname'] = $this->request->post['firstname'];
                    $payment_address['lastname'] = $this->request->post['lastname'];
                    $payment_address['company'] = '';
                    $payment_address['address_1'] = $this->request->post['address_1'];
                    $payment_address['address_2'] = $this->request->post['address_2'];
                    $payment_address['city'] = $this->request->post['city'];
                    $payment_address['postcode'] = $this->request->post['postcode'];
                    $payment_address['country_id'] = $this->request->post['country_id'];
                    $payment_address['zone_id'] = $this->request->post['zone_id'];

                     $this->session->data['payment_address_id'] = $this->model_account_address->addAddress($payment_address);
                }
            }
        }
        
      
        $this->response->setOutput(json_encode($json));
    }
    
    public function creditcardValidate($cc_type, $cc, $extra_check = false){
        $cards = array(
            "visa" => "(4\d{12}(?:\d{3})?)",
            "amex" => "(3[47]\d{13})",
            "jcb" => "(35[2-8][89]\d\d\d{10})",
            "discover" => "(6[0245]\d{14})",
            "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
            "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
            "mastercard" => "(5[1-5]\d{14})",
            "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
        );
        $names = array("visa", "american-express", "jcb", "discover", "maestro", "solo", "mastercard", "switch");
        $matches = array();
        $pattern = "#^(?:".implode("|", $cards).")$#";
        $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
        if($extra_check && $result > 0){
            $result = ($this->validatecard($cc))?1:0;
        }
       
        return ($result>0)? (strpos($cc_type,$names[sizeof($matches)-2]) !== false) :    false;    //return ($result>0)?$names[sizeof($matches)-2]:false;
    }
    
    public function validatecard($cardnumber) {
        $cardnumber=preg_replace("/\D|\s/", "", $cardnumber);  # strip any non-digits
        $cardlength=strlen($cardnumber);
        $parity=$cardlength % 2;
        $sum=0;
        for ($i=0; $i<$cardlength; $i++) {
          $digit=$cardnumber[$i];
          if ($i%2==$parity) $digit=$digit*2;
          if ($digit>9) $digit=$digit-9;
          $sum=$sum+$digit;
        }
        $valid=($sum%10==0);
        return $valid;
    }

}

?>
