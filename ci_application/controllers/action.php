<?php
/*---Performs DB_Manipulation Actions in the system---*/

class Action extends CI_Controller {

	/*
		Constructor
		-Loads Models and helpers
		-Load Generic Data files
	*/
	public function __construct() {
		parent::__construct();
		//Helpers
		$this->load->helper('url');
		$this->load->library('session');
		//Other Example Loads
		$this->load->model('action_model');
	}


	/*
		Login Using User passed data
	*/
	public function login() {
		$result = $this->action_model->login();
        if($result){ 	//if logged in, return json user info
        	$id = $this->session->userdata('userid');
        	$name = $this->session->userdata('username');
        	$email = $this->session->userdata('email');
            $data['json'] = '{"userid":'. $id .',"username":"'. $name .'","useremail":"'.$email.'"}';  
        }else{			//else return error
        	$this->output->set_status_header('400'); //Triggers the jQuery error callback
        	$data['json'] = '{"error":"Incorrect username or password"}';
        }   
        	$this->load->view('data/json_view', $data); 
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect(base_url());
	}
		
}