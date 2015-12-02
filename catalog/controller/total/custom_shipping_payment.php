<?php

class ControllerTotalCustomShippingPayment extends Controller {

    public function set_shipping_method($args = array()) {

        if (isset($args[0]) && !empty($args[0])) {
            $this->request->post['country_id'] = $args[0];
        } else {
            $this->request->post['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($args[1]) && !empty($args[1])) {
            $this->request->post['zone_id'] = $args[1];
        } else {
            $this->request->post['zone_id'] = $this->config->get('config_zone_id');
        }

        if (isset($args[2]) && !empty($args[2])) {
            $this->request->post['postcode'] = $args[2];
        } else {
            $this->request->post['postcode'] = '1111'; //because some shipping method need to have postcode set
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

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
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'address_1' => '',
            'address_2' => '',
            'postcode' => $this->request->post['postcode'],
            'city' => '',
            'zone_id' => $this->request->post['zone_id'],
            'zone' => $zone,
            'zone_code' => $zone_code,
            'country_id' => $this->request->post['country_id'],
            'country' => $country,
            'iso_code_2' => $iso_code_2,
            'iso_code_3' => $iso_code_3,
            'address_format' => $address_format
        );

        $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);
    }

    public function get_shipping_methods() {
        if ($this->config->get('shipping_status') && $this->cart->hasShipping() && (isset($this->session->data['shipping_address']))) {

            $this->load->language('total/shipping');

            $data['heading_title'] = 'Choose Shipping Method'; //$this->language->get('heading_title');

            $data['text_shipping'] = $this->language->get('text_shipping');
            $data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $data['text_select'] = $this->language->get('text_select');
            $data['text_none'] = $this->language->get('text_none');
            $data['text_loading'] = $this->language->get('text_loading');

            $data['entry_country'] = $this->language->get('entry_country');
            $data['entry_zone'] = $this->language->get('entry_zone');
            $data['entry_postcode'] = $this->language->get('entry_postcode');

            $data['button_quote'] = $this->language->get('button_quote');
            $data['button_shipping'] = $this->language->get('button_shipping');
            $data['button_cancel'] = $this->language->get('button_cancel');

            $data['text_shipping_method'] = $this->language->get('text_shipping_method');

            $quote_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error']
                        );

                        if (!(isset($this->request->post['shipping_method']) || isset($this->session->data['shipping_method']))) {
                            if (!isset($temp_sort_order)) {
                                $temp_shipping_method = $quote['quote'][$result['code']]['code'];
                                $temp_sort_order = $quote['sort_order'];
                            } else {
                                if ($quote['sort_order'] < $temp_sort_order) {
                                    $temp_shipping_method = $this->request->post['shipping_method'] = $quote['quote'][$result['code']]['code'];
                                    $temp_sort_order = $quote['sort_order'];
                                }
                            }
                        }
                    }
                }
            }

            if (isset($temp_shipping_method) && (!isset($this->request->post['shipping_method']) )) {
                $this->request->post['shipping_method'] = $temp_shipping_method;
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


            //if (isset($this->request->post['shipping_method']) && $this->validateShipping()) 
            if (isset($this->request->post['shipping_method'])) {
                $shipping = explode('.', $this->request->post['shipping_method']);
                $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
            }

            if (isset($this->session->data['shipping_method'])) {
                $data['shiping_method'] = $this->session->data['shipping_method']['code'];
            } else {
                $data['shiping_method'] = '';
            }


            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/total/view_checkout_shipping.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/total/view_checkout_shipping.tpl', $data);
            } else {
                return $this->load->view('default/template/total/view_checkout_shipping.tpl', $data);
            }
        }
    }

}
