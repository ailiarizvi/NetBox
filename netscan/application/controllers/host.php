<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Host extends CI_Controller{

	public $hosts=null;

	function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->scan();
		print_r($this->host);

	}

	private function scan(){
		require('netdiscover.php');
		$network = new netdiscover();
		$this->host = $network->index();
		print_r($this->host);
	}

	}
