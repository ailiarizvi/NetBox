<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Host extends CI_Controller{

	public $ip = '192.168.0.100';

	function __construct(){
		
		parent::__construct();
		$this->load->library('snmp');
	}

	public function index(){
		$this->load->view('host/index');
		//$this->getHostInfo();
	}

	public function getHostInfo(){

		$host = $this->snmp->getHostDetails($this->ip);
		print_r(json_encode($host));
		
	}

}
