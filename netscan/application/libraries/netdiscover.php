<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class NetDiscover {

	/**
	*
	*@Param _cmd : contains the netdiscover command
	*Access is private 
	*/

	private $_cmd='sudo netdiscover -P -i eth1 -r ';

	/**
	*
	*@Param _host : array used to store netdiscover output
	*Access is public 
	*/

	public $_hosts = null;

	/**
	*
	*Get the network addresss from the config file execute netdiscover command. it return an array which is then parse.
	*function returns the public variable host.
	*
	*@Param ci : assigning codeigniter object.
	*@Param networkAddress : get network address defined in config file.
	*@Param data : store netdiscover output .
	*@Param _hosts : returns parsed data.
	*
	*Access is public 
	*/

	public function getHosts($data) {
	//	$ci =& get_instance();
	//	$networkAddress = $ci->config->item('network_address');
		$networkAddress = $data;
		exec($this->_cmd.$networkAddress,$data);
		$this->_parse($data);
		return $this->_hosts;
		
	}

	/**
	*
	*This function accept netdiscover ouput as data. Return the array entries that match the ip.
	*Iterate this array and split string on white space and store it in the _hosts.
	*
	*@Param data : Returns array that matches ip
	*@Param networkInfo : split string on white space and returns an array
	*@Param _host : make an associative array. 
	*
	*Access is private
	*/

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