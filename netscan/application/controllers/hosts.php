<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Hosts extends CI_Controller {

	/**
	*
	*Load snmp library
	*@access public
 	*/

	public function __construct(){
		
		parent::__construct();
		$this->load->library('netdiscover');
		$this->load->library('snmp');
		$this->load->model('host');
	}

	/**
	*
	*call a view index. This view call gethostdetail function and display data in json format. 
	*Access is public
 	*/

	public function index(){
		$this->getHostInfo();
	//	$this->getHost();
	}

	/**
	*
	*This function is called by view. Returns a single host details in json format.
	*@Param ip : get ip from the url specified in view.
	*@Param host : store data in an array, return from the snmp library.
	* 
	*Access is public
 	*/


	public function getHostInfo(){

	$host = $this->netdiscover->getHosts();
	$server = $this->snmp->getHostDetails("localhost");
		$i=0;
		foreach ($host as $key => $value) {
			$host2 = $this->snmp->getHostDetails($value['ip']);
			$host1 = array('ip' => $value['ip'], 'mac' => $value['mac'], 'vendor' => $value['vendor'],'scandate' => date('j-m-y h-i-s'));
			$_host['server'] = $server;
			$_host['host'][$i] = array_merge($host1,$host2);
			$i++;
 		}
 	//	$this->host1->insertHost($_host);
 		echo "<pre>";
 		print_r($_host);

	}	
 			
	/**
	*Retrieve data from db.
	*Access is public
	*/

	public function getHost() {

		$host = $this->host1->getHost();
		while ($host->hasNext()) {
			$cursor = $host->getNext();
			unset($cursor['_id']);
			return $cursor;
		}
	}
}
