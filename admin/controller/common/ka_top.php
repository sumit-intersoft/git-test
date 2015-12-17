<?php
/*
	Project: Ka Extensions
	Author : karapuz <support@ka-station.com>

	Version: 2.1 ($Revision: 14 $)
*/

class ControllerCommonKaTop extends Controller {

	/*
		$data - exact copy of the $this->data array assigned to the parent controller;
	*/
	public function index($data) {
		$data = $data;
		return $this->load->view('common/ka_top.tpl', $data);
  	}
}
?>