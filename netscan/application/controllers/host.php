<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Host extends CI_Controller{

	
	function __construct(){
		
		parent::__construct();
		$this->load->library('snmp');
	}

	public function index(){
		$this->load->view('host/index');
	}

	public function getHostInfo(){

		$ip = $_GET['ip'];
		$host = $this->snmp->getHostDetails($ip);
	//	echo "<pre>";
		print_r(json_encode($host));

	}

}
