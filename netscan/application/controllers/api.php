<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

/**
*include REST_Controller to call their functions in another controller
*/
require_once APPPATH."libraries/REST_Controller.php";

/**
*class Api which extends another controller 
*/
class Api extends REST_Controller {

	public function __construct(){
		
		parent::__construct();
		$this->load->library('netdiscover');
		$this->load->library('snmp');
		$this->load->model('host');
	}
	
	/**
	*
	*Runs netdiscover get ip, mac, vendor. Run snmp on each ip getting single ip specifications. 
	*Give response to client.
	*
	*@access public
	*@param net - get network address from curl
	*@param subnet - get subnetmask from curl
 	*/
	public function index_get() {
	$host = $this->netdiscover->getHosts();
	$i=0;
	foreach ($host as $key => $value) {
		$host2 = $this->snmp->getHostDetails($value['ip']);
		$host1 = array('ip' => $value['ip'], 'mac' => $value['mac'], 'vendor' => $value['vendor']);
		$_host['network'] = array('workstations' => null,'servers' => null,'printers' => null,'routers' => null );
		$_host['Host'][$i] = array_merge($host1,$host2);
		$i++;
		}
		//$_host = $this->getHost();
 		if($_host) {  
            $this->response($_host, 200);  
        }  
        else {  
            $this->response(NULL, 404);  
        }
	}
	
	/**
	*
	*Run netdiscover and snmp getting all information.
	*store it in database.
	*
	*@access public
	*@param net - get network address from curl
	*@param subnet - get subnetmask from curl
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
 		$this->host1->insertHost($_host);
	}	

	/**
	*
	*Get host details from database. 
	*
	*@access public
	*@return json string
 	*/
	public function getHost() {
		$host = $this->host1->getHost();
		$i=0;
		while ($host->hasNext()) {
			$cursor = $host->getNext();
			unset($cursor['_id']);
			$hosts[$i] = $cursor;
			$i++;
		}
		return $hosts;
	}

	/**
	*
	*Run netdiscover and snmp getting all information.
	*store it in database.
	*
	*@access public
	*@param net - get network address from curl
	*@param subnet - get subnetmask from curl
 	*/
	public function scan_post() {
		$net = $this->post('network');
		$subnet =$this->post('subnet');
		$data = $net."/".$subnet;
		$host = $this->netdiscover->getHosts($data);
		$i=0;
		foreach ($host as $key => $value) {
			$host2 = $this->snmp->getHostDetails($value['ip']);
			$host1 = array('ip' => $value['ip'], 'mac' => $value['mac'], 'vendor' => $value['vendor'],'scandate' => date('j-m-y h-i-s'));
			$_host['network'] = array('workstations' => null,'servers' => null,'printers' => null,'routers' => null );
			$_host['Host'][$i] = array_merge($host1,$host2);
			$i++;
 		}
 	//	$this->host->insertHost($_host);
 	//	$_host = $this->getHost();
 		if($_host)  
        {  
            $this->response($_host, 200);  
        }  
  
        else  
        {  
            $this->response(NULL, 404);  
        }
	} 
}

