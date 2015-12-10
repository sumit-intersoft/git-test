<?php

class ControllerCheckoutCustomValidation extends Controller {
    
    public function signUpValidate() {
        $this->language->load('checkout/checkout');

        $this->load->model('account/customer');
        
        unset($this->session->data['register_account']);
        $json = array();
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart_custom');
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

    public function registerValidate() {
          
        $this->language->load('checkout/checkout');
        $this->load->model('account/customer');
        $this->load->model('account/address');
        $this->load->model('localisation/country');
        $this->load->model('account/custom_field');
        $this->load->model('account/activity');
        $json = array();
        
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }
        
        if (!$json) {
                
            $products = $this->cart->getProducts();
            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                        if ($product_2['product_id'] == $product['product_id']) {
                                $product_total += $product_2['quantity'];
                        }
                }

                if ($product['minimum'] > $product_total) {
                        $json['redirect'] = $this->url->link('checkout/cart');

                        break;
                }
            }

            if (!$json) {
                
                if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
                
                if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
				

				if (empty($this->request->post['payment_address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['payment_address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['payment_address_id']);

					
				}
		} else {
                    
                    if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
                            $json['error']['firstname'] = $this->language->get('error_firstname');
                    }

                    if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
                            $json['error']['lastname'] = $this->language->get('error_lastname');
                    }

                    if (!$this->customer->isLogged()) {
                        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
                                    $json['error']['telephone'] = $this->language->get('error_telephone');
                        }
                    }
                    
                    if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
                            $json['error']['address_1'] = $this->language->get('error_address_1');
                    }

                    if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
                            $json['error']['city'] = $this->language->get('error_city');
                    }

                    

                    $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

                    if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
                            $json['error']['postcode'] = $this->language->get('error_postcode');
                    }

                    if ($this->request->post['country_id'] == '') {
                            $json['error']['country'] = $this->language->get('error_country');
                    }

                    if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                            $json['error']['zone'] = $this->language->get('error_zone');
                    }

                    // Custom field validation
                   

                    $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

                    foreach ($custom_fields as $custom_field) {
                            if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']]))                             {
                                    $json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                            }
                    }

                    
		}
                
 
                if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
				
				if (empty($this->request->post['shipping_address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['shipping_address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping_address_id']);

					
				}
		} else {
                    
                        if ((utf8_strlen(trim($this->request->post['shipping_firstname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_firstname'])) > 32)) {
                                $json['error']['shipping_firstname'] = $this->language->get('error_firstname');
                        }

                        if ((utf8_strlen(trim($this->request->post['shipping_lastname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_lastname'])) > 32)) {
                                $json['error']['shipping_lastname'] = $this->language->get('error_lastname');
                        }

                        if ((utf8_strlen(trim($this->request->post['shipping_address_1'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_lastname'])) > 128)) {
                                $json['error']['shipping_address_1'] = $this->language->get('error_address_1');
                        }

                        if ((utf8_strlen(trim($this->request->post['shipping_city'])) < 2) || (utf8_strlen(trim($this->request->post['shipping_city'])) > 128)) {
                                $json['error']['shipping_city'] = $this->language->get('error_city');
                        }

                        $this->load->model('localisation/country');

                        $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);

                        if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['shipping_postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_postcode'])) > 10)) {
                                $json['error']['postcode'] = $this->language->get('error_postcode');
                        }

                        if ($this->request->post['shipping_country_id'] == '') {
                                $json['error']['country'] = $this->language->get('error_country');
                        }

                        if (!isset($this->request->post['shipping_zone_id']) || $this->request->post['shipping_zone_id'] == '') {
                                $json['error']['zone'] = $this->language->get('error_zone');
                        }

                        // Custom field validation
                        $this->load->model('account/custom_field');

                        $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

                        foreach ($custom_fields as $custom_field) {
                                if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['shipping_custom_field'][$custom_field['custom_field_id']])) {
                                        $json['error']['shipping_custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                                }
                        }
                
                }
                
                
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
                
                if (!$json) { 
                    if((!isset($this->session->data['shipping_method'])) || (empty($this->session->data['shipping_method'])))
                    {
                        $json['error']['warning'] = $this->language->get('error_shipping');
                     }    
                }
                
                if(!$json){

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
                      $customer_id = 0;
                      
                    if(!$this->customer->isLogged()){
                                $this->request->post['fax'] = ''; //because we don't have fax filled but addcusotmer expect fax field
                                $customer_id = $this->model_account_customer->addCustomer($this->request->post);

                                // Clear any previous login attempts for unregistered accounts.
                                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

                                $this->session->data['account'] = 'register';

                                $this->load->model('account/customer_group');

                                $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

                                if ($customer_group_info && !$customer_group_info['approval']) {
                                        $this->customer->login($this->request->post['email'], $this->request->post['password']);

                                        // Default Payment Address
                                        $this->load->model('account/address');

                                        $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

                                        if (!empty($this->request->post['shipping_address'])) {
                                                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                                        }
                                } else {
                                        $json['redirect'] = $this->url->link('account/success');
                                }

                                // Add to activity log
                                $this->load->model('account/activity');

                                $activity_data = array(
                                        'customer_id' => $customer_id,
                                        'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
                                );

                                $this->model_account_activity->addActivity('register', $activity_data);
                        }
                        
                        

                    if($this->customer->isLogged()){

                        if(!$customer_id) {   //if we crete a new customer no need to insert new address for payment
                            if (  ( isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'new') ){
                                
                                        $address_id = $this->model_account_address->addAddress($this->request->post);

                                        $this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);

                                        $this->load->model('account/activity');

                                        $activity_data = array(
                                                'customer_id' => $this->customer->getId(),
                                                'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
                                        );

                                        $this->model_account_activity->addActivity('address_add', $activity_data);
                            }
                       }

                        if(isset($this->request->post['is_shipping_same'])  && ($this->request->post['is_shipping_same'] == 1 )) { 

                            $this->session->data['shipping_address'] = $this->session->data['payment_address'] ;

                        } elseif (  (!isset($this->request->post['shipping_address']))     ||   ( isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'new') ) {

                            $shipping_address['customer_id'] = $this->customer->getId();
                            $shipping_address['firstname'] =   (isset($this->request->post['shipping_firstname']) ? $this->request->post['shipping_firstname'] : '' );
                            $shipping_address['lastname'] =  (isset($this->request->post['shipping_lastname']) ? $this->request->post['shipping_lastname'] : '' );
                            $shipping_address['company'] = (isset($this->request->post['shipping_company']) ? $this->request->post['shipping_company'] : '' );
                            $shipping_address['address_1'] = (isset($this->request->post['shipping_address_1']) ? $this->request->post['shipping_address_1'] : '' ); 
                            $shipping_address['address_2'] = (isset($this->request->post['shipping_address_2']) ? $this->request->post['shipping_address_2'] : '' );
                            $shipping_address['city'] = (isset($this->request->post['shipping_city']) ? $this->request->post['shipping_city'] : '' ); 
                            $shipping_address['postcode'] = (isset($this->request->post['shipping_postcode']) ? $this->request->post['shipping_postcode'] : '' );
                            $shipping_address['country_id'] = (isset($this->request->post['shipping_country_id']) ? $this->request->post['shipping_country_id'] : '' );
                            $shipping_address['zone_id'] = (isset($this->request->post['shipping_zone_id']) ? $this->request->post['shipping_zone_id'] : '' );
                            
                            $address_id = $this->model_account_address->addAddress($shipping_address);

                            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);

                            $activity_data = array(
                                 'customer_id' => $this->customer->getId(),
                                 'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
                             );

                             $this->model_account_activity->addActivity('address_add', $activity_data);

                    }

                    }  else { 
                        $json['redirect'] = $this->url->link('checkout/cart');
                    }        
                                
                }
            }
        }
       
        $this->response->setOutput(json_encode($json));
        
    }
    
}

?>
