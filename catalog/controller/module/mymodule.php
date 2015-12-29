<?php
class ControllerModuleMyModule extends Controller {
    public function on_customer_add($customer_id) {
        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        $admin_mail = $this->config->get('config_email');
        mail($admin_mail, "New Customer", "A new customer has just registered with the following e-mail: " . $customer_info['email']);
    }
    
    public function on_customer_edit($customer_id) {
        echo  ' esfsd '; exit;
        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        $admin_mail = $this->config->get('config_email');
        mail($admin_mail, "New Customer", "A new customer has just registered with the following e-mail: " . $customer_info['email']);
    }
}