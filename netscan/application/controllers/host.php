<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Host extends CI_Controller {


	function __construct() {
		
		parent::__construct();
		$this->load->library('netdiscover');
	}

	public function index() {
		$this->load->view('network/index');

	}

	private function scan() {
		$hosts = $this->netdiscover->getHosts();
		print_r(json_encode($hosts));
		
	}

}
