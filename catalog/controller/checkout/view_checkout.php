<?php

class ControllerCheckoutViewCheckout extends Controller {

    private $error = array();

    public function index() {

        $settings = $this->config->get('customcheckout');

        if (isset($settings) && $settings == 2) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }
        $this->language->load('checkout/view_checkout');
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        $this->language->load('checkout/checkout');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'] = array();

        $this->document->addScript('catalog/view/javascript/jquery/autotab/jquery.autotab.js');
         
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/view_checkout'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $data['text_step1'] = $this->language->get('text_step1');
        $data['text_step2'] = $this->language->get('text_step2');
        $data['text_step3'] = $this->language->get('text_step3');

        $data['text_heading_billing'] = $this->language->get('text_heading_billing');
        $data['text_heading_shipping'] = $this->language->get('text_heading_shipping');
        $data['text_billing_shipping'] = $this->language->get('text_billing_shipping');
        $data['text_fname'] = $this->language->get('text_fname');
        $data['text_lname'] = $this->language->get('text_lname');
        $data['text_address_1'] = $this->language->get('text_address_1');
        $data['text_address_2'] = $this->language->get('text_address_2');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_state'] = $this->language->get('text_state');
        $data['text_city'] = $this->language->get('text_city');
        $data['text_country'] = $this->language->get('text_country');
        $data['text_zip'] = $this->language->get('text_zip');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_credit_cart'] = $this->language->get('text_credit_cart');
        $data['text_paypal'] = $this->language->get('text_paypal');
        $data['text_affirm'] = $this->language->get('text_affirm');
        $data['text_payment_info'] = $this->language->get('text_payment_info');
        
        
        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');
        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_address_existing'] = $this->language->get('text_address_existing');
        $data['text_address_new'] = $this->language->get('text_address_new');
        $data['text_select'] = $this->language->get('text_select');


        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_company_id'] = $this->language->get('entry_company_id');
        $data['entry_tax_id'] = $this->language->get('entry_tax_id');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');

        $data['button_continue'] = $this->language->get('button_continue');
        
        $data['shopping_cart'] = $this->url->link('checkout/cart');

        $data['shipping_required'] = $this->cart->hasShipping();
        
        $data['login_module']  =  $this->load->controller('checkout/view_checkout_login');
         
        $this->load->model('account/address');
        $data['logged'] = $logged = $this->customer->isLogged();
        $data['addresses'] = array();
        $data['text_none'] = $this->language->get('text_none');
        if ($logged) {
            $data['addresses'] = $this->model_account_address->getAddresses();
            
            if (isset($this->session->data['payment_address']) && (!$this->session->data['payment_address']['address_id']) ) {
                    unset($this->session->data['payment_address']);
            }
            
            if (isset($this->session->data['payment_address']) && ($this->session->data['payment_address']['address_id']) ) {
                $data['payment_address'] = $this->session->data['payment_address'];
            } else {
                $data['payment_address'] = $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
            
            if ($this->config->get('shipping_status') && $this->cart->hasShipping()) {
                if (isset($this->session->data['shipping_address']) && (!$this->session->data['shipping_address']['address_id']) ) {
                        unset($this->session->data['shipping_address']);
                }

                if (isset($this->session->data['shipping_address']) && ($this->session->data['shipping_address']['address_id']) ) {
                    $data['shipping_address'] = $this->session->data['shipping_address'];
                } else {
                    $data['shipping_address'] = $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }
            }
            
        } else {
            if (file_exists(DIR_APPLICATION . '/controller/total/custom_shipping_payment.php')) {
                
                    $this->load->controller('total/custom_shipping_payment/set_payment_address');
                      $data['payment_address'] = $this->session->data['payment_address'];
                    
                    if ($this->config->get('shipping_status') && $data['shipping_required']) { 
                            $this->load->controller('total/custom_shipping_payment/set_shipping_address');
                            $data['shipping_address'] = $this->session->data['shipping_address'];
                    }
            }
        }

        if ($this->config->get('shipping_status') && $data['shipping_required'] && (!empty($data['shipping_address']))) {
            if (file_exists(DIR_APPLICATION . '/controller/total/custom_shipping_payment.php')) {
                $data['shipping'] = $this->load->controller('total/custom_shipping_payment/get_shipping_methods');
            }
        }
        
        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods']; 
        } else {
			$data['shipping_methods'] = array();
	}
                
        
        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        $this->language->load('checkout/checkout');

        $this->load->model('account/address');

       
        //// payment methods
        $data['payment_methods'] = '';
        if (!empty($data['payment_address'])) {
            // Totals
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

            // Payment Methods
            $method_data = array();

            $results = $this->model_extension_extension->getExtensions('payment');
            
            $cart_has_recurring = $this->cart->hasRecurringProducts();

            foreach ($results as $result) {
                
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($data['payment_address'], $total);
                    
                    if ($method) {
                        if($result['code'] == 'stripe') {
                            $method['payment_form'] = $this->load->controller('payment/stripe/payment_form');
                        } else {
                            $method['payment_form'] = '';
                        }
                        if ($cart_has_recurring > 0) {
                            if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments')) {
                                if ($this->{'model_payment_' . $result['code']}->recurringPayments() == true) {
                                    $method_data[$result['code']] = $method;
                                }
                            }
                        } else {
                            $method_data[$result['code']] = $method;
                        }
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);
            
            $this->session->data['payment_methods'] = $method_data;
            $data['payment_methods'] = $method_data;
        }

        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/checkout.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/view_checkout.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/view_checkout.tpl', $data));
        }
    }
    
    public function setPaymentAddress() {

        $json = array();
        
        if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
            $this->load->model('account/address');
            if (in_array($this->request->post['payment_address_id'], array_keys($this->model_account_address->getAddresses()))) {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['payment_address_id']);
            }
        } else {
           
                if (isset($this->request->post['country_id']) && !empty($this->request->post['country_id'])) {
                   $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$this->request->post['country_id'] . "'");

                   if ($country_query->num_rows) {
                           $country = $country_query->row['name'];
                           $iso_code_2 = $country_query->row['iso_code_2'];
                           $iso_code_3 = $country_query->row['iso_code_3'];
                           $address_format = $country_query->row['address_format'];
                   } else {
                           $country = '';
                           $iso_code_2 = '';
                           $iso_code_3 = '';
                           $address_format = '';
                   }

                   $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$this->request->post['zone_id'] . "'");

                   if ($zone_query->num_rows) {
                           $zone = $zone_query->row['name'];
                           $zone_code = $zone_query->row['code'];
                   } else {
                           $zone = '';
                           $zone_code = '';
                   }

                   $this->session->data['payment_address']  = array(
                                           'address_id'     => 0,
                                           'firstname'      => $this->request->post['firstname'],
                                           'lastname'       => $this->request->post['lastname'],
                                           'company'        => $this->request->post['company'],
                                           'address_1'      => $this->request->post['address_1'],
                                           'address_2'      => $this->request->post['address_2'],
                                           'postcode'       => $this->request->post['postcode'],
                                           'city'           => $this->request->post['city'],
                                           'zone_id'        => $this->request->post['zone_id'],
                                           'zone'           => $zone,
                                           'zone_code'      => $zone_code,
                                           'country_id'     => $this->request->post['country_id'],
                                           'country'        => $country,
                                           'iso_code_2'     => $iso_code_2,
                                           'iso_code_3'     => $iso_code_3,
                                           'address_format' => $address_format,
                                           'custom_field'   => json_decode('[]', true)
                                   );
                }
        }
        
        $this->response->setOutput(json_encode($json));
    }
    
    public function setShippingAddress() {

        $json = array();
        
        if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
            $this->load->model('account/address');
            if (in_array($this->request->post['shipping_address_id'], array_keys($this->model_account_address->getAddresses()))) {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping_address_id']);
            }
        } else {
                if (isset($this->request->post['shipping_country_id']) ) {
                   $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$this->request->post['shipping_country_id'] . "'");

                   if ($country_query->num_rows) {
                           $country = $country_query->row['name'];
                           $iso_code_2 = $country_query->row['iso_code_2'];
                           $iso_code_3 = $country_query->row['iso_code_3'];
                           $address_format = $country_query->row['address_format'];
                   } else {
                           $country = '';
                           $iso_code_2 = '';
                           $iso_code_3 = '';
                           $address_format = '';
                   }

                   $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$this->request->post['shipping_zone_id'] . "'");

                   if ($zone_query->num_rows) {
                           $zone = $zone_query->row['name'];
                           $zone_code = $zone_query->row['code'];
                   } else {
                           $zone = '';
                           $zone_code = '';
                   }

                   $this->session->data['shipping_address']  = array(
                                           'address_id'     => 0,
                                           'firstname'      => $this->request->post['shipping_firstname'],
                                           'lastname'       => $this->request->post['shipping_lastname'],
                                           'company'        => $this->request->post['shipping_company'],
                                           'address_1'      => $this->request->post['shipping_address_1'],
                                           'address_2'      => $this->request->post['shipping_address_2'],
                                           'postcode'       => $this->request->post['shipping_postcode'],
                                           'city'           => $this->request->post['shipping_city'],
                                           'zone_id'        => $this->request->post['shipping_zone_id'],
                                           'zone'           => $zone,
                                           'zone_code'      => $zone_code,
                                           'country_id'     => $this->request->post['shipping_country_id'],
                                           'country'        => $country,
                                           'iso_code_2'     => $iso_code_2,
                                           'iso_code_3'     => $iso_code_3,
                                           'address_format' => $address_format,
                                           //'custom_field'   => json_decode($address_query->row['custom_field'], true)
                                   );
                }
        }
        $this->response->setOutput(json_encode($json));
    }
    
    public function getCart() {
         
        $this->language->load('checkout/checkout');
        $this->language->load('checkout/cart_custom_two');
        


        $this->document->setTitle($this->language->get('heading_title'));

        $data['text_step1'] = $this->language->get('text_step1');
        $data['text_step2'] = $this->language->get('text_step2');
        $data['text_step3'] = $this->language->get('text_step3');

        $data['text_heading_billing'] = $this->language->get('text_heading_billing');
        $data['text_heading_shipping'] = $this->language->get('text_heading_shipping');
        $data['text_billing_shipping'] = $this->language->get('text_billing_shipping');
        $data['text_fname'] = $this->language->get('text_fname');
        $data['text_lname'] = $this->language->get('text_lname');
        $data['text_address_1'] = $this->language->get('text_address_1');
        $data['text_address_2'] = $this->language->get('text_address_2');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_state'] = $this->language->get('text_state');
        $data['text_city'] = $this->language->get('text_city');
        $data['text_country'] = $this->language->get('text_country');
        $data['text_zip'] = $this->language->get('text_zip');
        $data['text_select'] = $this->language->get('text_select');
        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        
      
        $this->language->load('checkout/checkout');

        $this->load->model('account/address');

        $taxes = $this->cart->getTaxes();
        foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
						'day'        => $this->language->get('text_day'),
						'week'       => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month'      => $this->language->get('text_month'),
						'year'       => $this->language->get('text_year'),
					);

					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}

					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}

				$data['products'][] = array(
					'cart_id'    => $product['cart_id'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'recurring'  => $recurring,
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
					'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
					'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				);
			}
            
            
       // Totals
        $total_data = array();
        $total = 0;

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

        foreach ($total_data as $total) {
                            $data['totals'][] = array(
                                    'title' => $total['title'],
                                    'text'  => $this->currency->format($total['value']),
                            );
                    }
        // Payment Methods
        $method_data = array();

        $this->load->model('extension/extension');

        $data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                        $data['vouchers'][] = array(
                                'description' => $voucher['description'],
                                'amount'      => $this->currency->format($voucher['amount'])
                        );
                }
        }
        
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/view_checkout_getcart.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/view_checkout_getcart.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/view_checkout_getcart.tpl', $data));
        }



        /* end generating cart */
    }
    
 
}
?>