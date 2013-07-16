<?php
if(!defined('BASEPATH')) exit('no direct script access allowed');

class Users extends CI_Controller{

	function __construct(){
		
		parent::__construct();
		session_start();
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->model('users/user');
	}

	function index() {
		
		$this->load->view('users/index');
	}

	function login() {

		$username = $this->input->post('email');
		$password = $this->input->post('password');
		$password = md5($password);
		$user = $this->user->authenticate($username, $password);
		if(empty($user)) {			
			$this->load->view('users/index');
			echo "incorrect username or password";
			echo "<a href='http://localhost/netbox/netscan/users/forgetPassword'>Forget password</a>";
		}

		else {			
			echo "you are logged in";
			echo "<a href='http://localhost/netbox/netscan/users/logout'>logout</a>";
			echo "</br>";		
			$sessionData = $this->session->all_userdata();	
			$data['name'] = $sessionData['name'];
			echo $data['name'];
			$this->rememberme();			
		}
	}

	function forgetPassword() {
		
		$this->load->view('users/forgetPassword');
	}

	function setPassword() {	
		
		$username = $this->input->post('email');
		$newPassword = $this->input->post('newpass');
		$rewritePassword = $this->input->post('writepass');
		if($newPassword == $rewritePassword) {
			$newPassword = md5($newPassword);
			$this->user->setPassword($username, $newPassword);
			echo "new password has created";
		}
		else {
		echo "password are not matching";
		}
	}

	function remeberMe() {
		 
		if (isset($_POST['rememberme'])) {
            setcookie('username', $_POST['username'], time()+60*60*24*365);
            setcookie('password', md5($_POST['password']), time()+60*60*24*365);
            return true;
        } 
        else {            
            setcookie('username', $_POST['username'], '', '', '', false);
            setcookie('password', md5($_POST['password']), '', '', '', false);
            return false;
        }
	}

	function logout() {
		
		session_destroy();
		$this->load->view('users/index');
	}		
}