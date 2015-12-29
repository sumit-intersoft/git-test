<?php
class ControllerModuleMyModule extends Controller {
    public function install() {
        $this->load->model('extension/event');
        //$this->model_extension_event->addEvent('mymodule', 'pre.admin.store.delete', 'module/mymodule/on_store_delete');
       // $this->model_extension_event->addEvent('mymodule', 'post.customer.add', 'module/mymodule/on_customer_add');
        $this->model_extension_event->addEvent('mymodule', 'post.customer.add', 'module/mymodule/on_customer_edit');
    }
    
    public function uninstall() {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('mymodule');
    }
    
    public function on_store_delete($store_id) {
        $this->load->model('setting/store');
        $store_info = $this->model_setting_store->getStore($store_id);
        $admin_mail = $this->config->get('config_email');
        mail($admin_mail, "A store has been deleted", "The store " . $store_info['url'] . " was deleted.");
    }
}