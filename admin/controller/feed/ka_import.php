<?php
/*
  Project: CSV Product Import
  Author : karapuz <support@ka-station.com>

  Version: 3 ($Revision: 124 $)

*/

require_once(DIR_SYSTEM . 'engine/ka_controller.php');

class ControllerFeedKaImport extends KaController {

	protected $extension_version = '3.7.5';
	protected $min_store_version = '1.5.1';
	protected $max_store_version = '1.5.6.9';
	protected $tables;
	protected $db_exists = false;

	protected function init() {
		
 		$this->tables = array(
 			'product' => array(
 				'fields' => array(),
 				'indexes' => array(
 					'model' => array()
 				)
 			),

 			'ka_product_import' => array(
				'is_new' => true,
 				'fields' => array(
 					'product_id' => array(
 						'type' => 'int(11)',
 					),
 					'token' => array(
 						'type' => 'varchar(255)',
 					),
 					'is_new' => array(
 						'type' => 'tinyint(1)',
 						'query' => "ALTER TABLE `" . DB_PREFIX . "ka_product_import` ADD `is_new` TINYINT(1) NOT NULL DEFAULT '0'"
 					),
 					'added_at' => array(
 						'type' => 'timestamp'
 					)
 				)
 			),
 			
 			'ka_import_records' => array(
 				'is_new' => true,
 				'fields' => array(
 					'product_id' => array(
 						'type' => 'int(11)',
 					),
 					'record_type' => array(
 						'type' => 'int(6)',
 					),
 					'record_id' => array(
 						'type' => 'int(11)',
 					),
 					'token' => array(
 						'type' => 'varchar(255)',
 					),
 					'added_at' => array(
 						'type' => 'timestamp',
 						'query' => "ALTER TABLE `" . DB_PREFIX . "ka_import_records` ADD added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
 					),
 				),
 				'indexes' => array(
 					'product_id' => array(),
 					'trr' => array(),
 				),
 			),		
 			
 			'ka_import_profiles' => array(
 				'is_new' => true,
 				'fields' => array(
  					'import_profile_id' => array(
  						'type' => 'int(11)',
  					),
  					'name' => array(
  						'type' => 'varchar(128)',
  					),
  					'params' => array(
  						'type' => 'mediumtext',
  					),
  				),
				'indexes' => array(
					'PRIMARY' => array(
						'query' => "ALTER TABLE `" . DB_PREFIX . "ka_import_profiles` ADD PRIMARY KEY (`import_profile_id`)",
					),
					'name' => array(
						'query' => "ALTER TABLE `" . DB_PREFIX . "ka_import_profiles` ADD INDEX (`name`)",
					),
				),
			),
 			
		);

		$this->tables['product']['indexes']['model']['query'] = 
			"ALTER TABLE " . DB_PREFIX . "product ADD INDEX (`model`)";


		$this->tables['ka_product_import']['query'] = "
			CREATE TABLE `" . DB_PREFIX . "ka_product_import` (
				`product_id` int(11) NOT NULL,
				`token` varchar(255) NOT NULL,
				`is_new` tinyint(1) NOT NULL default 0,
				`added_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
				PRIMARY KEY  (`product_id`,`token`)
			);
		";

		$this->tables['ka_import_profiles']['query'] = "
			CREATE TABLE `" . DB_PREFIX . "ka_import_profiles` (
			  `import_profile_id` int(11) NOT NULL auto_increment,
			  `name` varchar(128) NOT NULL,
			  `params` mediumtext NOT NULL,
			  PRIMARY KEY  (`import_profile_id`),
			  KEY `name` (`name`) 
			) DEFAULT CHARSET=utf8
		";
		
		$this->tables['ka_import_records']['query'] = "
			CREATE TABLE `" . DB_PREFIX . "ka_import_records` (  
				`product_id` int(11) NOT NULL,  
				`record_type` int(6) NOT NULL,  
				`record_id` int(11) NOT NULL,  
				`token` varchar(255) NOT NULL,  
				`added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				KEY `product_id` (`product_id`),  
				KEY `trr` (`token`,`record_type`,`record_id`)) DEFAULT CHARSET=utf8
		";
		
		return true;
	}

	public function index() {
		$this->loadLanguage('feed/ka_import');

		$heading_title = $this->language->get('heading_title_plain');
		$this->document->setTitle($heading_title);

		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$val = max(5, $this->request->post['ka_pi_update_interval']);
			$this->request->post['ka_pi_update_interval'] = min(25, $val);
			if (!isset($this->request->post['ka_pi_create_options'])) {
				$this->request->post['ka_pi_create_options'] = '';
			}
			if (!isset($this->request->post['ka_pi_enable_product_id'])) {
				$this->request->post['ka_pi_enable_product_id'] = '';
			}
			if (!isset($this->request->post['ka_pi_skip_img_download'])) {
				$this->request->post['ka_pi_skip_img_download'] = '';
			}

			$this->request->post['ka_import_status'] = 'Y';

			$this->model_setting_setting->editSetting('ka_import', $this->request->post);
			$this->session->data['success'] = $this->language->get('Settings have been stored sucessfully.');
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$data['heading_title']         = $heading_title;
		$data['extension_version']     = $this->extension_version;
	
		$data['button_save']           = $this->language->get('button_save');
		$data['button_cancel']         = $this->language->get('button_cancel');

		$data['ka_pi_update_interval']    = $this->config->get('ka_pi_update_interval');
		$data['ka_pi_general_separator']         = $this->config->get('ka_pi_general_separator');
		$data['ka_pi_multicat_separator']         = $this->config->get('ka_pi_multicat_separator');
		$data['ka_pi_related_products_separator'] = $this->config->get('ka_pi_related_products_separator');
		$data['ka_pi_options_separator']          = $this->config->get('ka_pi_options_separator');
		$data['ka_pi_image_separator']            = $this->config->get('ka_pi_image_separator');
		
		$data['ka_pi_create_options']     = $this->config->get('ka_pi_create_options');
		$data['ka_pi_enable_product_id']  = $this->config->get('ka_pi_enable_product_id');
		$data['ka_pi_skip_img_download']  = $this->config->get('ka_pi_skip_img_download');
		
		$data['ka_pi_key_fields']         = $this->config->get('ka_pi_key_fields');		
		if (!is_array($data['ka_pi_key_fields']) || empty($data['ka_pi_key_fields'])) {
			$data['ka_pi_key_fields'] = array('model');
		}
		$data['key_fields'] = array(
			array(
				'field' => 'model',
				'name'  => 'model',
			),
			array(
				'field' => 'sku',
				'name'  => 'sku',
			),
			array(
				'field' => 'upc',
				'name'  => 'upc',
			),
		);
		
		$data['ka_pi_status_for_new_products']      = $this->config->get('ka_pi_status_for_new_products');
		$data['ka_pi_status_for_existing_products'] = $this->config->get('ka_pi_status_for_existing_products');

 		$data['breadcrumbs'] = array();

 		$data['breadcrumbs'][] = array(
   			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
   			'separator' => false
 		);

	  	$data['breadcrumbs'][] = array(
   			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
   			'separator' => ' :: '
	  	);
		
 		$data['breadcrumbs'][] = array(
   			'text'      => $heading_title,
			'href'      => $this->url->link('feed/ka_import', 'token=' . $this->session->data['token'], 'SSL'),
   			'separator' => ' :: '
 		);
		
		$data['action'] = $this->url->link('feed/ka_import', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

		$data['import_page']        = $this->url->link('tool/ka_import', 'token=' . $this->session->data['token'], 'SSL');
		$data['is_vqmod_available'] = $this->isVQModAvailable();

//		$this->template = 'feed/ka_import.tpl';
//		$this->children = array(
//			'common/header',
//			'common/footer'
//		);
//				
//		$this->response->setOutput($this->render());
                
                $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('feed/ka_import.tpl', $data));
	}
	
	protected function validate() {
	
		if (!$this->user->hasPermission('modify', 'feed/ka_import')) {
			$this->addTopMessage($this->language->get('error_permission'), 'E');
			return false;
			
		} elseif (empty($this->request->post['ka_pi_key_fields'])) {
			$this->addTopMessage($this->language->get('Key fields cannot be empty'), 'E');
			return false;
		}

		return true;		
	}

	protected function isVQModAvailable() {

		if (class_exists('VQModObject')) {
			return true;
		}

		return false;
	}

	protected function checkDBCompatibility(&$messages) {

		if (empty($this->tables)) {
			return true;
		}

		$this->db_exists = false;
		foreach ($this->tables as $tk => $tv) {

			$tbl = DB_PREFIX . $tk;
			
			//Joomla uses a dynamic table prefix but their 'smart' replacement does not 
			// work for 'SHOW TABLES LIKE'. We have to deal with this situation.
			//
			if (class_exists('MijoShop')) {
			  $tbl = MijoShop::get('db')->getDbo()->replacePrefix($tbl);
			}

			// specific code for the import extension
			//
			if (in_array($tk, array('ka_product_import','ka_import_profiles'))) {
				$this->db->query("DROP TABLE IF EXISTS `$tbl`");
			}
			
			$res = $this->db->query("SHOW TABLES LIKE '$tbl'");

			if (empty($res->rows)) {
				if (empty($tv['is_new'])) {
					$messages .= "Table '$tbl' is not found in DB. Administrator action is required.";
					return false;
			 	}
				continue;
			}

			// existing table is being checked.
      
			if (!empty($tv['is_new'])) {
				$this->tables[$tk]['exists'] = true;
			}

			$fields = $this->db->query("DESCRIBE `$tbl`");
			if (empty($fields->rows)) {
				$messages .= "Table '$tbl' exists in the database but it is empty. Please remove this table and try to install the extension again.";
				return false;
			}

			// check fields 
			//
			$db_fields = array();
			foreach ($fields->rows as $v) {
				$db_fields[$v['Field']] = array(
					'type'  => $v['Type']
				);
			}

			foreach ($tv['fields'] as $fk => $field) {
				if (empty($db_fields[$fk])) {
					if ($this->db_patched) {
						$messages .= "Required changes are present in DB but they are not valid. Field '$fk' is not found in the table '$tbl'.";
						return false;
					}
					continue;
				}

				// if the field is found we validate its type
				//
				$db_field = $db_fields[$fk];

				if ($field['type'] != $db_field['type']) {
					$messages .= "Field type '$db_field[type]' for '$fk' in the table '$tbl' does not match the required field type '$field[type]'.";
					return false;
				} else {
					$this->tables[$tk]['fields'][$fk]['exists'] = true;
					continue;
				}
			}

			// check indexes

			if (!empty($tv['indexes'])) {

				$rec = $this->db->query("SHOW INDEXES FROM `$tbl`");
				$db_indexes = array();
				foreach ($rec->rows as $v) {
					$db_indexes[$v['Key_name']] = array(
						'columns' => $v['Column_name']
					);
				}

				foreach ($tv['indexes'] as $ik => $index) {
					if (!empty($db_indexes[$ik])) {
						$this->tables[$tk]['indexes'][$ik]['exists'] = true;
					}
				}
			}
		}

		return true;
	}


	protected function patchDB(&$messages) {

		// create db
		if (empty($this->tables)) {
			return true;
		}

		foreach ($this->tables as $tk => $tv) {
			if (!empty($tv['is_new'])) {
				if (empty($tv['exists'])) {
					$this->db->query($tv['query']);
					continue;
				}
			}

			if (!empty($tv['fields'])) {
				foreach ($tv['fields'] as $fk => $fv) {
					if (empty($fv['exists'])) {
						$this->db->query($fv['query']);
					}
				}
			}

			if (!empty($tv['indexes'])) {
				foreach ($tv['indexes'] as $ik => $iv) {
					if (empty($iv['exists'])) {
						$this->db->query($iv['query']);
					}
				}
			}
		}
		
		return true;
	}


	protected function checkCompatibility(&$messages) {
		$messages = '';

		// check store version 
		if (version_compare(VERSION, $this->min_store_version) < 0 
			|| version_compare(VERSION, $this->max_store_version) > 0)
		{
			$messages .= "compatibility of this extension with your store version (" . VERSION . ") was not checked.
				please contact an author of the extension for update.";
		}

		//check database
		if (!$this->checkDBCompatibility($messages)) {
			return false;
		}
    
		return true;
	}


	public function install() {

		$this->init();

		$this->load->model('extension/extension');
		$this->load->model('setting/setting');
		
		$success = false;
		$messsages = '';
		if ($this->checkCompatibility($messages)) {
			if (!$this->db_patched) {
				if ($this->patchDB($messages)) {
					$success = true;
				}
			} else {
				$success = true;
			}

			if ($success) {
				// grant permissions to the import page automatically
				$this->load->model('user/user_group');
				$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'tool/ka_import');
				$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'tool/ka_import');

				$settings = array(
					'ka_pi_update_interval'            => 10,
					'ka_pi_related_products_separator' => ':::',
					'ka_pi_options_separator'          => ':::',
					'ka_pi_image_separator'            => ':::',
					'ka_pi_multicat_separator'         => ':::',
					'ka_pi_general_separator'          => ':::',
					'ka_import_status'                 => 'Y',
					'ka_pi_create_options'             => 'Y',
					'ka_pi_key_fields'                 => array('model'),
					'ka_pi_skip_img_download'          => '',
				);
				$this->model_setting_setting->editSetting('ka_import', $settings);
			}
		}

		if (!$success) {
			$this->model_setting_extension->uninstall('feed', 'ka_import');
			$this->model_setting_setting->deleteSetting('ka_import');

			$this->session->data['error'] = 'Extension installation failed. List of errors are below:<br/>' . $messages;
		} else {
			$this->session->data['success'] = 'Extension is installed successfully.';
		}
	}

	public function uninstall() {
		
	}
}
?>