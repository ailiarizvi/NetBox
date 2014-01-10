<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class host extends CI_Model {
 
	function __construct() {
		
		parent::__construct();
		$tableName = 'host';

		$this->connection = new Mongo(); // Connect to Mongo
		$this->db = $this->connection->NetScan; // Select a database
		$this->collection = $this->db->$tableName; // Select a collection to store a host info
		}

	 function insertHost($host) {
 		
 	//	$data = $this->hostSchema($host);
 		$this->collection->batchInsert($host);
	 }

	 function getHost() {
 		
 		$host = $this->collection->find();
 		return $host;
	 }


		function hostSchema($host) {

			$_host = array(
				'ip' => $host['ip'],
				'mac' => $host['mac'],
				'vendor' => $host['vendor'],
				'scandate' => date('j-m-y h-i-s'),
				'computername' => $host['computerName'],
				'description' => $host['description'],
				'os' => $host['os'],
				'hosttype' => $host['deviceType'],
				'uptime' => $host['uptime'],
				'systemtime' => $host['systemTime'],
				'cpuUtilization' => $host['cpuUtilization'],
				'router' => $host['routers'],
				'bandwidthUtilization' => $host['bandwidthUtilization'],
				'diskType' => $host['diskType'],
				'swid' => array( 
					'_id' => new MongoId,
					'status' => $host['installedSoftwares']
					), 
				'interfaces' => $host['interfaces'], // pass interface array in the format as specified in databse design
				'services' => $host['services'], // same as above
				'storagedevices' => $host['storageDevices'],
				'ports' => $host['ports']
				);
			$this->collection->batchInsert($_host);
			/*if($this->collection->insert($this->classhost)){
				return true;
			}else {
				return false;
			}*/
		}

	/*	function softwareSchema() {

			$this->_schemaSoftware = array(
				'swid' => $this->_schemaHost['swid']['_id'],
				'swname' => $swname,
				'swtype' => $swtype,
				'Installationdate' => $installationdate,
				);
		}

		function configSchema() {

			$this->_schemaConfig = $config;
		}

		function scanlogSchema() {

			$this->_schemaScanlog = $scanlog;
		}

		function userSchema() {

			$this->schemaUser = array(
				'_id' => new MongoId ,
				'firstname' => $firstname,
				'lastname' => $lastname,
				'email' => $email,
				'password' => md5($password) ,
				'createdat' => date('j-m-y-h-i-s') ,
				'lastlogindate' => date(),
				'modifiedat' => date(),
				'salt' => $salt,
				'token' => $token,
				);
		}
*/
}