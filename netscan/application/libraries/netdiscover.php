<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class NetDiscover {

	private $_cmd='sudo netdiscover -P -i eth0 -r ';

	public $_hosts = null;

	public function getHosts() {
		$ci =& get_instance();
		$networkAddress = $ci->config->item('network_address');
		exec($this->_cmd.$networkAddress,$data);
		$this->_parse($data);
		return $this->_hosts;
		
	}

	private function _parse($data) {

		$data=preg_grep('([19][2-9]|[0][0-9][0-9]|[1][0-9][0-9]\.[25][0-5]|[2][0-4][0-9]|[1]\d\d\.[25][0-5]|[2][0-4][0-9]|[1]\d\d\.[22][0-3]|[2][0-1][0-9]|[1]\d\d)', $data);
		
		$i=0;
			foreach ($data as $value) {
				$networkInfo=preg_split('/[\s]+/', $value, 5, PREG_SPLIT_NO_EMPTY);
				$this->_hosts[$i]['ip']=$networkInfo[0];
				$this->_hosts[$i]['mac']=$networkInfo[1];
				$this->_hosts[$i]['vendor']=$networkInfo[4];
				$i++;
			}
			
	}

}

/**
*TODO
*Exception handling
*
*/