<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Network extends CI_Controller{


	function __construct(){
		
		parent::__construct();
		$this->load->library('netdiscover');
	}

	public function index(){
		$this->load->view('network/index');
		
	}

	public function getHosts(){

		$hosts = $this->netdiscover->getHosts();
		print_r(json_encode($hosts));
		
	}

}
