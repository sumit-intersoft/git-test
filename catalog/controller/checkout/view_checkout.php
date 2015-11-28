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

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart_custom_two'),
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
        $data['text_user_telephone'] = $this->language->get('text_user_telephone');
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
        
        
        /* extra code  */
        $data['text_credit_card_type'] = $this->language->get('text_credit_card_type');
        $data['text_credit_card_number'] = $this->language->get('text_credit_card_number');
        $data['text_credit_card_verification'] = $this->language->get('text_credit_card_verification');
        $data['text_credit_card_expires'] = $this->language->get('text_credit_card_expires');
        /* extra code  */

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

        // error message
        if (isset($this->session->data['error']['payment']) && !empty($this->session->data['error']['payment'])) {
            $data['error_warning'] = $this->session->data['error']['payment'];
            unset($this->session->data['error']['payment']);
        } else {
            $data['error_warning'] = '';
        }

        
        
        $data['logged'] = $logged = $this->customer->isLogged();
        $data['addresses'] = array();
        $data['text_none'] = $this->language->get('text_none');
        if ($logged) {
            $this->load->model('account/address');
            
            if (isset($this->session->data['payment_address']) && (!$this->session->data['payment_address']['address_id']) ) {
                    unset($this->session->data['payment_address']);
            }
            
            if (isset($this->session->data['payment_address']) && ($this->session->data['payment_address']['address_id']) ) {
                $data['payment_address'] = $this->session->data['payment_address'];
            } else {
                $data['payment_address'] = $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
            
            //----------------- Old code that need to be removed --------------------------------->
                if (!$this->session->data['payment_address_id'])
                    $this->session->data['payment_address_id'] = $this->customer->getAddressId();

                if (!$this->session->data['shipping_address_id'])
                    $this->session->data['shipping_address_id'] = $this->customer->getAddressId();

                if (!$this->session->data['shipping_address_id'])
                    $data['address_id'] = $this->customer->getAddressId();
                else
                    $data['address_id'] = $this->session->data['shipping_address_id'];
                $data['addresses'] = $this->model_account_address->getAddresses();
                
                /*--------------------------  Old code  ends here ------------------------------------- */
            } else {
                    $data['shipping'] = '';
                    if ($this->config->get('shipping_status') && $this->cart->hasShipping() && ( (!isset($this->session->data['shipping_methods'])) || (!isset($this->session->data['shipping_method'])))) {
                        $files = glob(DIR_APPLICATION . '/controller/total/shipping.php');
                        if ($files) {
				$extension = basename($files[0], '.php');
                                $data['shipping'] = $this->load->controller('total/' . $extension. '/custom_checkout_shipping');
                        }
                    }
            /* I think no need for this */
            $this->request->post['country_id'] = $this->config->get('config_country_id');
            $this->request->post['zone_id'] = $this->config->get('config_zone_id');
            $this->request->post['postcode'] = '1111'; 
            
            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

            if ($country_info) {
                    $country = $country_info['name'];
                    $iso_code_2 = $country_info['iso_code_2'];
                    $iso_code_3 = $country_info['iso_code_3'];
                    $address_format = $country_info['address_format'];
            } else {
                    $country = '';
                    $iso_code_2 = '';
                    $iso_code_3 = '';
                    $address_format = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                    $zone = $zone_info['name'];
                    $zone_code = $zone_info['code'];
            } else {
                    $zone = '';
                    $zone_code = '';
            }

            $this->session->data['shipping_address'] = array(
                    'firstname'      => '',
                    'lastname'       => '',
                    'company'        => '',
                    'address_1'      => '',
                    'address_2'      => '',
                    'postcode'       => $this->request->post['postcode'],
                    'city'           => '',
                    'zone_id'        => $this->request->post['zone_id'],
                    'zone'           => $zone,
                    'zone_code'      => $zone_code,
                    'country_id'     => $this->request->post['country_id'],
                    'country'        => $country,
                    'iso_code_2'     => $iso_code_2,
                    'iso_code_3'     => $iso_code_3,
                    'address_format' => $address_format
            );

            $quote_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('shipping');

            foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('shipping/' . $result['code']);

                            $quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                            if ($quote) {
                                    $quote_data[$result['code']] = array(
                                            'title'      => $quote['title'],
                                            'quote'      => $quote['quote'],
                                            'sort_order' => $quote['sort_order'],
                                            'error'      => $quote['error']
                                    );
                            }
                    }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);

            $this->session->data['shipping_methods'] = $quote_data;

            if (isset($this->session->data['shipping_methods'])) {
                $data['shipping_methods'] = $this->session->data['shipping_methods'];
            } else {
                $data['shipping_methods'] = array();
            }
            
            
		/*  Ends here*/
        }


        if (isset($this->session->data['billing_country_id'])) {
            $data['country_id'] = $this->session->data['billing_country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }


        if (isset($this->session->data['shipping_country_id'])) {
            $data['shipping_country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $data['shipping_country_id'] = $this->config->get('config_country_id');
        }


        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        ///// Shipping methods

        $this->language->load('checkout/checkout');

        $this->load->model('account/address');

        if ($logged && isset($this->session->data['shipping_address_id'])) {
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } else {
            $shipping_address = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'));
        }

        if (!empty($shipping_address)) {
            // Shipping Methods
            $quote_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);
            $this->session->data['shipping_methods'] = $quote_data;
            
            
            
            if (isset($this->session->data['shipping_methods'])) {
                $data['shipping_methods'] = $this->session->data['shipping_methods'];
            } else {
                $data['shipping_methods'] = array();
            }
            
            
            
            if (isset($this->session->data['shipping_method_selected'])) {
                $data['code'] = $this->session->data['shipping_method_selected'];
            } else {
                $data['code'] = '';
            }

            if (empty($this->session->data['shipping_methods'])) {
                $data['error_warning_shipping'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
            } else {
                $data['error_warning_shipping'] = '';
            }
            
            /*  old code ends here*/
            
            $this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('shipping/' . $result['code']);

					$quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$quote_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data['shipping_methods'] = $quote_data;
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
        }

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

        //// payment methods

        if ($logged && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } else {
            $payment_address = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'));
        }

        if (!empty($payment_address)) {
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

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('payment');

            $cart_has_recurring = $this->cart->hasRecurringProducts();

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

                    if ($method) {
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

        if (empty($this->session->data['payment_methods'])) {
            $data['payment_methods'] = '';
        }




        /* generating cart */
        $data['products'] = array();

        $products = $this->cart->getProducts();
        $this->load->model('tool/image');
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
            }

            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }

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
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            // Display prices
            if (($this->config->get('config_customer_price') && $logged) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $price = false;
            }

            // Display prices
            if (($this->config->get('config_customer_price') && $logged) || !$this->config->get('config_customer_price')) {
                $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
            } else {
                $total = false;
            }

            $recurring = '';

            if ($product['recurring']) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year'),
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
                'cart_id' => $product['cart_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'recurring' => $recurring,
                'quantity' => $product['quantity'],
                'stock' => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
                'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                'price' => $price,
                'total' => $total,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        $data['affirm_status'] = $this->config->get('affirm_status');
        $data['affirm_info_page_link'] = $this->url->link('information/information', 'information_id=21');
        $data['shopping_cart'] = $this->url->link('checkout/cart');

        $data['shipping_required'] = $this->cart->hasShipping();
        
        
        /* varibal that are fixed */
        $data['zone_id'] = '';
        $data['shipping_zone_id']= '';
        /*  fixing end here*/
        
        
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
    
    public function setpaymentshipping() {

        $json = array();
        
        $this->session->data['shipping-address-new'] = 1;
        $this->session->data['shipping_address']['country_id'] =  $this->request->post['shipping_country_id'];
        $this->session->data['shipping_address']['zone_id'] = $this->request->post['shipping_zone_id'];

        $this->response->setOutput(json_encode($json));
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
                                           //'custom_field'   => json_decode($address_query->row['custom_field'], true)
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
            
                if (isset($this->request->post['shipping_country_id']) && !empty($this->request->post['shipping_country_id'])) {
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
    
    
    public function validatePaymentShipping() {

        $json = array();
        $this->load->model('account/address');
        if (isset($this->request->post['payment_address_id']) && !empty($this->request->post['payment_address_id'])) {
            $this->session->data['payment_address_id'] = $this->request->post['payment_address_id'];
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
            if ($payment_address) {
                $this->session->data['payment_country_id'] = $payment_address['country_id'];
                $this->session->data['payment_zone_id'] = $payment_address['zone_id'];
            }
            unset($this->session->data['payment-address-new']);
        }

        if (isset($this->request->post['shipping_address_id']) && !empty($this->request->post['shipping_address_id'])) {
            $this->session->data['shipping_address_id'] = $this->request->post['shipping_address_id'];
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            if ($shipping_address) {
                $this->session->data['shipping_country_id'] = $shipping_address['country_id'];
                $this->session->data['shipping_zone_id'] = $shipping_address['zone_id'];
            }
            unset($this->session->data['shipping-address-new']);
        }

        $this->response->setOutput(json_encode($json));
    }

     public function getCart() {
        $this->language->load('checkout/checkout');
        $this->language->load('checkout/cart_custom_two');
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->response->redirect($this->url->link('checkout/cart_custom'));
        }


        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart_custom_two'),
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

        // error message
        if (isset($this->session->data['error']['payment']) && !empty($this->session->data['error']['payment'])) {
            $data['error_warning'] = $this->session->data['error']['payment'];
            unset($this->session->data['error']['payment']);
        } else {
            $data['error_warning'] = '';
        }
        $data['logged'] = $logged = $this->customer->isLogged();
        if ($logged) {

            $this->language->load('checkout/checkout');
            $data['text_address_existing'] = $this->language->get('text_address_existing');
            $data['text_address_new'] = $this->language->get('text_address_new');
            $data['text_select'] = $this->language->get('text_select');
            $data['text_none'] = $this->language->get('text_none');

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

            $data['button_continue'] = $this->language->get('button_continue');

            if (!$this->session->data['payment_address_id'])
                $this->session->data['payment_address_id'] = $this->customer->getAddressId();

            if (!$this->session->data['shipping_address_id'])   //may need to unset $this->session->data['shipping-address-new'])
                $this->session->data['shipping_address_id'] = $this->customer->getAddressId();

            if (!$this->session->data['shipping_address_id'])
                $data['address_id'] = $this->customer->getAddressId();
            else
                $data['address_id'] = $this->session->data['shipping_address_id'];





            $data['addresses'] = array();

            $this->load->model('account/address');

            $data['addresses'] = $this->model_account_address->getAddresses();
        }


        if (isset($this->session->data['billing_country_id'])) {
            $data['country_id'] = $this->session->data['billing_country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }


        if (isset($this->session->data['shipping_country_id'])) {
            $data['shipping_country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $data['shipping_country_id'] = $this->config->get('config_country_id');
        }


        $this->load->model('localisation/country');
        
        $data['products'] = array();
        $this->load->model('tool/image');

        $data['countries'] = $this->model_localisation_country->getCountries();

        ///// Shipping methods

        $this->language->load('checkout/checkout');

        $this->load->model('account/address');

        if ($logged && isset($this->session->data['shipping_address_id'])) {
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } else {
            $shipping_address = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'));
        }
        if (!empty($shipping_address)) {
            // Shipping Methods
            $quote_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);
            $this->session->data['shipping_methods'] = $quote_data;
            if (isset($this->session->data['shipping_methods'])) {
                $data['shipping_methods'] = $this->session->data['shipping_methods'];
            } else {
                $data['shipping_methods'] = array();
            }
            if (isset($this->session->data['shipping_method_selected'])) {
                $data['code'] = $this->session->data['shipping_method_selected'];
            } else {
                $data['code'] = '';
            }

            if (empty($this->session->data['shipping_methods'])) {
                $data['error_warning_shipping'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
            } else {
                $data['error_warning_shipping'] = '';
            }
        }


        //// payment methods

        if ($logged && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } else {
            $payment_address = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'));
        }

        if (!empty($payment_address)) {
            
            
        $products = $this->cart->getProducts();
        
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
            
            foreach ($total_data as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value']),
				);
			}
            // Payment Methods
            $method_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('payment');

            $cart_has_recurring = $this->cart->hasRecurringProducts();

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total);

                    if ($method) {
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

        
        $data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			}
        
        
        if (empty($this->session->data['payment_methods'])) {
            $data['payment_methods'] = '';
        }


        $this->load->model('extension/extension');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/view_checkout_getcart.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/view_checkout_getcart.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/view_checkout_getcart.tpl', $data));
        }



        /* end generating cart */
    }
    
    
   /* protected function validateCoupon() {
        $this->load->model('checkout/coupon');

        $coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

        if (!$coupon_info) {
            $this->error['warning'] = $this->language->get('error_coupon');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }*/

   /*protected function validateShipping() {
        if (!empty($this->request->post['shipping_method'])) {
            $shipping = explode('.', $this->request->post['shipping_method']);

            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $this->error['warning'] = $this->language->get('error_shipping');
            }
        } else {
            $this->error['warning'] = $this->language->get('error_shipping');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }*/

   /* public function add() {
        $this->language->load('checkout/cart_custom');

        $json = array();

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (isset($this->request->post['quantity'])) {
                $quantity = $this->request->post['quantity'];
            } else {
                $quantity = 1;
            }

            if (isset($this->request->post['option'])) {
                $option = array_filter($this->request->post['option']);
            } else {
                $option = array();
            }

            if (isset($this->request->post['profile_id'])) {
                $profile_id = $this->request->post['profile_id'];
            } else {
                $profile_id = 0;
            }

            $product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

            foreach ($product_options as $product_option) {
                if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                    $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                }
            }

            $profiles = $this->model_catalog_product->getProfiles($product_info['product_id']);

            if ($profiles) {
                $profile_ids = array();

                foreach ($profiles as $profile) {
                    $profile_ids[] = $profile['profile_id'];
                }

                if (!in_array($profile_id, $profile_ids)) {
                    $json['error']['profile'] = $this->language->get('error_profile_required');
                }
            }

            if (!$json) {
                $this->cart->add($this->request->post['product_id'], $quantity, $option, $profile_id);

                $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                // Totals
                $this->load->model('extension/extension');

                $total_data = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();

                // Display prices
                if (($this->config->get('config_customer_price') && $logged) || !$this->config->get('config_customer_price')) {
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

                        $sort_order = array();

                        foreach ($total_data as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $total_data);
                    }
                }

                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
            } else {
                $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
            }
        }

        $this->response->setOutput(json_encode($json));
    }
    */
   
   /*
    public function apllyCoupon() {
        $this->language->load('checkout/cart_custom_two');
        $json = array();
        if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
            $this->session->data['coupon'] = $this->request->post['coupon'];

            $json['sucess'] = $this->language->get('text_coupon');
        } else {
            $json['error'] = $this->language->get('error_coupon');
        }

        $this->response->setOutput(json_encode($json));
    }
*/
    
}

?>
