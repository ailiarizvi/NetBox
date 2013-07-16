<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class User extends CI_Model {
 
	function __construct() {
		
		parent::__construct();
		$tableName = 'users';
		$this->connection = new Mongo(); // Connect to Mongo
		$this->db = $this->connection->NetScan; // Select a database
		$this->collection = $this->db->$tableName; // Select a collection
		}
 
 	function insertDb() {
 		$user = array(
			'name' => 'Luke Skywalker',
			'username' => 'jedimaster23',
			'password' => md5('usetheforce'),
			'createdAt' => date('j-m-y h-i-s'),
			'modifiedAt' => date('j-m-y h-i-s'),
			'salt' => md5('security-concerned')
			);
			$this->collection->insert($user);
	 }
	

	function authenticate($username, $password) {

		$user = $this->collection->findone(array('username' => $username , 'password' => $password));
		if (empty($user)) {
			return False;
		}
	/*	$_SESSION['user_id'] = (string) $user['_id'];
		$_SESSION['user'] = (string) $user['name'];*/
		
		$this->session->set_userdata(array(
			'id' => (string) $user['_id'],
			'username' => $user['username'],
			'name' => $user['name'],
			'isloggedin' => true
			));
		return True;
	}

	function setPassword($username, $password) {
		$user = array('username' => $username);
		if(empty($user)){
			return false;
		}
		else {
			$newdata = array('$set' => array("password" => $password));
			$this->collection->update(array('username' => $username), $newdata);
			return true;
		}
		
	}

}
