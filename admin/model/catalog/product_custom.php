<?php
class ModelCatalogProductCustom extends Model {
        private $table_name = 'product_custom_field';
        
        public function createTable() {
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `".DB_PREFIX."$this->table_name` (
                    `product_id` int(11) NOT NULL,
                    `msrp` decimal(15,4) NOT NULL DEFAULT '0.0000',
                    `item_type` varchar(255) NOT NULL,
                    PRIMARY KEY  (`product_id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
               ");  
        }
        
        public function deleteTable() {
            
            $this->db->query("
               DROP TABLE IF EXISTS `".DB_PREFIX."$this->table_name` "
            );  
        }
    
        
        public function addProduct($product_id, $data) {
            
             $this->db->query("INSERT INTO " . DB_PREFIX . "$this->table_name SET product_id = '" . (int)$product_id . "',  msrp = '" . (float)$data['msrp'] . "', item_type = '" . $this->db->escape($data['item_type']). "'");
             
        }

	public function editProduct($product_id, $data) {
            
		$this->db->query("DELETE FROM " . DB_PREFIX . "$this->table_name WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "$this->table_name SET product_id = '" . (int)$product_id . "',  msrp = '" . (float)$data['msrp'] . "', item_type = '" . $this->db->escape($data['item_type']). "'");
                
        }

        
        public function deleteProduct($product_id) {
            
            $this->db->query("DELETE FROM " . DB_PREFIX . "$this->table_name WHERE product_id = '" . (int)$product_id . "'");
            
        }

}
