<?php

class ControllerModuleProductEvents extends Controller {
    
    public function install() {

        $this->load->model('extension/event');
        $this->load->model('catalog/product_custom');
        
        $this->model_extension_event->addEvent('product', 'post.admin.product.add', 'module/product_events/add');
        $this->model_extension_event->addEvent('product', 'post.admin.product.edit', 'module/product_events/edit');
        $this->model_extension_event->addEvent('product', 'post.admin.product.delete', 'module/product_events/delete');
        
        $this->model_catalog_product_custom->createTable();
    }

    public function uninstall() {
        $this->load->model('extension/event');
        $this->load->model('catalog/product_custom');
        
        $this->model_extension_event->deleteEvent('product');
        $this->model_catalog_product_custom->deleteTable();
        
    }

    public function add($product_id) {
        $this->load->model('catalog/product_custom');
        $this->model_catalog_product_custom->addProduct($product_id,$this->request->post);
    }

    public function edit($product_id) {

        $this->load->model('catalog/product_custom');
        $this->model_catalog_product_custom->editProduct($product_id, $this->request->post);
        
    }

    public function delete($product_id) {

        $this->load->model('catalog/product_custom');
        $this->model_catalog_product_custom->deleteProduct($product_id);
    }

}
