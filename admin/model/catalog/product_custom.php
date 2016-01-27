<?php
class ModelCatalogProductCustom extends Model {
        private $table_name = 'product_custom_field';
        
        public function createTable() {
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `".DB_PREFIX."$this->table_name` (
                    `product_id` int(11) NOT NULL,
                    `msrp` decimal(15,4) NOT NULL DEFAULT '0.0000',
                    `metal_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
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
            
             $this->db->query("INSERT INTO " . DB_PREFIX . "$this->table_name SET product_id = '" . (int)$product_id . "',  msrp = '" . (float)$data['msrp'] . "', metal_price = '" . (float)$data['metal_price'] . "', item_type = '" . $this->db->escape($data['item_type']). "'");
             
        }

	public function editProduct($product_id, $data) {
            
		$this->db->query("DELETE FROM " . DB_PREFIX . "$this->table_name WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "$this->table_name SET product_id = '" . (int)$product_id . "',  msrp = '" . (float)$data['msrp'] . "', metal_price = '" . (float)$data['metal_price'] . "', item_type = '" . $this->db->escape($data['item_type']). "'");
                
        }

        public function deleteProduct($product_id) {
            
            $this->db->query("DELETE FROM " . DB_PREFIX . "$this->table_name WHERE product_id = '" . (int)$product_id . "'");
            
        }
        
        public function getProduct($product_id) {
		$query = $this->db->query("SELECT  pc.* FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "$this->table_name pc ON (p.product_id = pc.product_id) WHERE p.product_id = '" . (int)$product_id . "'");

		return $query->row;
	}

}
