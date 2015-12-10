<?php 

class ControllerCheckoutCartCustomConfirm extends Controller { 
	public function index() {
            
		$redirect = '';
                $json = array();
                
		if ($this->cart->hasShipping()) {
			// Validate if shipping address has been set.		
			$this->load->model('account/address');

			if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {					
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		
			} elseif (isset($this->session->data['guest'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			}

			if (empty($shipping_address)) {								
				$redirect = $this->url->link('checkout/cart_custom_two', '', 'SSL');
			}

			// Validate if shipping method has been set.	
			if (!isset($this->session->data['shipping_method'])) {
				$redirect = $this->url->link('checkout/cart_custom_two', '', 'SSL');
			}
		} else {
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}

		// Validate if payment address has been set.
		$this->load->model('account/address');

		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}	

		if (empty($payment_address)) {
			$redirect = $this->url->link('checkout/checkout', '', 'SSL');
		}			

		// Validate if payment method has been set.	
		if (!isset($this->session->data['payment_method'])) {
			$redirect = $this->url->link('checkout/cart_custom_two', '', 'SSL');
		}

		// Validate cart has products and has stock.	
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$redirect = $this->url->link('checkout/cart');				
		}	

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
				$redirect = $this->url->link('checkout/cart');

				break;
			}				
		}

		if (!$redirect) {
                         
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();

			$this->load->model('extension/extension');

			$sort_order = array(); 

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			$sort_order = array(); 

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
                       
			array_multisort($sort_order, SORT_ASC, $total_data);

			$this->language->load('checkout/checkout');

			$data = array();

			$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$data['store_id'] = $this->config->get('config_store_id');
			$data['store_name'] = $this->config->get('config_name');

			if ($data['store_id']) {
				$data['store_url'] = $this->config->get('config_url');		
			} else {
				$data['store_url'] = HTTP_SERVER;	
			}

			if ($this->customer->isLogged()) {
//				$data['customer_id'] = $this->customer->getId();
//				$data['customer_group_id'] = $this->customer->getCustomerGroupId();
//				$data['firstname'] = $this->customer->getFirstName();
//				$data['lastname'] = $this->customer->getLastName();
//				$data['email'] = $this->customer->getEmail();
//				$data['telephone'] = $this->customer->getTelephone();
//				$data['fax'] = $this->customer->getFax();
//
//				$this->load->model('account/address');
//
//				$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
//                                
//                                $this->load->model('account/customer');

                                /* new code */
                            $this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$data['customer_id'] = $this->customer->getId();
				$data['customer_group_id'] = $customer_info['customer_group_id'];
				$data['firstname'] = $customer_info['firstname'];
				$data['lastname'] = $customer_info['lastname'];
				$order_data['email'] = $customer_info['email'];
				$data['telephone'] = $customer_info['telephone'];
				$data['fax'] = $customer_info['fax'];
				$data['custom_field'] = json_decode($customer_info['custom_field'], true);
                                
                                 /* new code  ends here*/
                                
                                
                                
			} elseif (isset($this->session->data['guest'])) {
				$data['customer_id'] = 0;
				$data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
				$data['firstname'] = $this->session->data['guest']['firstname'];
				$data['lastname'] = $this->session->data['guest']['lastname'];
				$data['email'] = $this->session->data['guest']['email'];
				$data['telephone'] = $this->session->data['guest']['telephone'];
				$data['fax'] = $this->session->data['guest']['fax'];

				$payment_address = $this->session->data['guest']['payment'];
			}

			$data['payment_firstname'] = $payment_address['firstname'];
			$data['payment_lastname'] = $payment_address['lastname'];	
			$data['payment_company'] = $payment_address['company'];	
			$data['payment_company_id'] = $payment_address['company_id'];	
			$data['payment_tax_id'] = $payment_address['tax_id'];	
			$data['payment_address_1'] = $payment_address['address_1'];
			$data['payment_address_2'] = $payment_address['address_2'];
			$data['payment_city'] = $payment_address['city'];
			$data['payment_postcode'] = $payment_address['postcode'];
			$data['payment_zone'] = $payment_address['zone'];
			$data['payment_zone_id'] = $payment_address['zone_id'];
			$data['payment_country'] = $payment_address['country'];
			$data['payment_country_id'] = $payment_address['country_id'];
			$data['payment_address_format'] = $payment_address['address_format'];

			if (isset($this->session->data['payment_method']['title'])) {
				$data['payment_method'] = $this->session->data['payment_method']['title'];
			} else {
				$data['payment_method'] = '';
			}

                        
			if (isset($this->session->data['payment_method']['code'])) {
				$data['payment_code'] = $this->session->data['payment_method']['code'];
			} else {
				$data['payment_code'] = '';
			}
                        
                        
			if ($this->cart->hasShipping()) {
				if ($this->customer->isLogged()) {
					$this->load->model('account/address');

					$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);	
				} elseif (isset($this->session->data['guest'])) {
					$shipping_address = $this->session->data['guest']['shipping'];
				}			

				$data['shipping_firstname'] = $shipping_address['firstname'];
				$data['shipping_lastname'] = $shipping_address['lastname'];	
				$data['shipping_company'] = $shipping_address['company'];	
				$data['shipping_address_1'] = $shipping_address['address_1'];
				$data['shipping_address_2'] = $shipping_address['address_2'];
				$data['shipping_city'] = $shipping_address['city'];
				$data['shipping_postcode'] = $shipping_address['postcode'];
				$data['shipping_zone'] = $shipping_address['zone'];
				$data['shipping_zone_id'] = $shipping_address['zone_id'];
				$data['shipping_country'] = $shipping_address['country'];
				$data['shipping_country_id'] = $shipping_address['country_id'];
				$data['shipping_address_format'] = $shipping_address['address_format'];

				if (isset($this->session->data['shipping_method']['title'])) {
					$data['shipping_method'] = $this->session->data['shipping_method']['title'];
				} else {
					$data['shipping_method'] = '';
				}

				if (isset($this->session->data['shipping_method']['code'])) {
					$data['shipping_code'] = $this->session->data['shipping_method']['code'];
				} else {
					$data['shipping_code'] = '';
				}				
			} else {
				$data['shipping_firstname'] = '';
				$data['shipping_lastname'] = '';	
				$data['shipping_company'] = '';	
				$data['shipping_address_1'] = '';
				$data['shipping_address_2'] = '';
				$data['shipping_city'] = '';
				$data['shipping_postcode'] = '';
				$data['shipping_zone'] = '';
				$data['shipping_zone_id'] = '';
				$data['shipping_country'] = '';
				$data['shipping_country_id'] = '';
				$data['shipping_address_format'] = '';
				$data['shipping_method'] = '';
				$data['shipping_code'] = '';
			}
                        
			$product_data = array();
			$products_main = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}

                     
//            foreach ($this->cart->getProducts() as $main_product_key => $mainproduct) {
//                foreach ($mainproduct as $keys => $subproducts_list) {
//                    $j = 1;
//                    foreach ($subproducts_list as $product) {
//
//                        $products_main[$main_product_key][$keys]['main_product_name'] = $product['main_product_name'];
//                        $products_main[$main_product_key][$keys]['name'] .= $product['name'];
//                        if ($j < count($subproducts_list)) {
//                            $products_main[$main_product_key][$keys]['name'] .= ',';
//                        }
//                        $products_main[$main_product_key][$keys]['option'] = $product['option'];
//                        $products_main[$main_product_key][$keys]['subproducts'][] = array(
//                            'key' => $product['key'],
//                            'product_id' => $product['product_id'],
//                            'name' => $product['name'],
//                            'quantity' => $product['quantity'],
//                            'download' => $product['download'],
//                            'model' => $product['model'],
//                            'subtract' => $product['subtract'],
//                            'price' => $product['price'],
//                            'total' => $product['total'],
//                            'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
//                            'reward' => $product['reward']
//                        );
//                        $j++;
//                    }
//                }
//            }
            

            // Gift Voucher
			$voucher_data = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$voucher_data[] = array(
						'description'      => $voucher['description'],
						'code'             => substr(md5(mt_rand()), 0, 10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],						
						'amount'           => $voucher['amount']
					);
				}
			}  

			$data['products'] = $products_main;
			$data['vouchers'] = $voucher_data;
			$data['totals'] = $total_data;
			$data['comment'] = $this->session->data['comment'];
			$data['total'] = $total;
                        $data['custom_payment_method'] = $this->session->data['custom_payment_method'];
			$data['card_type'] = $this->session->data['card_detail']['card_type'];
			$data['card_no'] = $this->session->data['card_detail']['card_no'];
			$data['cvv'] = $this->session->data['card_detail']['cvv'];
			$data['card_expiry_month'] = $this->session->data['card_detail']['card_expiry_month'];
			$data['card_expiry_year'] = $this->session->data['card_detail']['card_expiry_year'];
                       
			if (isset($this->request->cookie['tracking'])) {
				$this->load->model('affiliate/affiliate');

				$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
				$subtotal = $this->cart->getSubTotal();

				if ($affiliate_info) {
					$data['affiliate_id'] = $affiliate_info['affiliate_id']; 
					$data['commission'] = ($subtotal / 100) * $affiliate_info['commission']; 
				} else {
					$data['affiliate_id'] = 0;
					$data['commission'] = 0;
				}
			} else {
				$data['affiliate_id'] = 0;
				$data['commission'] = 0;
			}

			$data['language_id'] = $this->config->get('config_language_id');
			$data['currency_id'] = $this->currency->getId();
			$data['currency_code'] = $this->currency->getCode();
			$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
			$data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];	
			} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];	
			} else {
				$data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];	
			} else {
				$data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];	
			} else {
				$data['accept_language'] = '';
			}

			$this->load->model('checkout/order');
                        
                        $order_data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}
                       
			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
                         
//                        if($data['payment_code'] == 'cod')
//                        { 
//                            if($this->session->data['order_id']){
//                              $this->model_checkout_customorder->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
//                          }
//                        } 
//                        
//                        if($data['payment_code'] == 'pp_standard')
//                        { 
//                            if($this->session->data['order_id']){
//                                $this->model_checkout_customorder->update_order_status($this->session->data['order_id'], 87);    //set order status to 'paypal pending' in case of paypal payment method
//                            }
//                        }
//                        
//                        
//                        
//                        if($data['payment_code'] == 'affirm')
//                        { 
//                            if($this->session->data['order_id']){
//                                $this->model_checkout_customorder->update_order_status($this->session->data['order_id'], $this->config->get('affirm_pending_status_id'));    //set order status to 'affirm pending' in case of affirm payment method
//                            }
//                        }
                        
                        
                        
			$this->data['column_name'] = $this->language->get('column_name');
			$this->data['column_model'] = $this->language->get('column_model');
			$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
			$this->data['column_total'] = $this->language->get('column_total');

			$this->data['text_recurring_item'] = $this->language->get('text_recurring_item');
			$this->data['text_payment_profile'] = $this->language->get('text_payment_profile');

			$this->data['products'] = array();

			

			// Gift Voucher
			$this->data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$this->data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			}  

			$this->data['totals'] = $total_data;
                               
                        $json['payment'] = $this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
                
                          $this->response->setOutput(json_encode($json));

		} else {
                    
			$this->data['redirect'] = $redirect;
                        $json['redirect'] = $redirect;
                        $this->response->setOutput(json_encode($json));
		}			

		
	}
}
?>