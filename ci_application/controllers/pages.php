<?php
class Pages extends CI_Controller {
	/*Constructor
		-Loads Models and helpers
		-Load Generic Data files
	*/
	public function __construct() {
		parent::__construct();
		//url helper: for public urls
		$this->load->helper('url');
		//Other Example Loads
		$this->load->model('data_model');

		//basic css and js
		$data['csFiles'] = array();
		$data['jsFiles'] = array();
    	$this->load->vars($data);
	}

	//Default page Query, Redirect to home
	public function index() {
		$this->homepage();
	}

	//site homepage
	public function homepage() {
		$data['headerTitle'] = 'PatchWork - Make a Difference';
		$data['pageTitle'] = 'Home';

		//$data['claimTags'] = $this->data_model->addClaim();
		
		$data['csFiles'] = array('general','homepage','ccStyles');
		$data['jsFiles'] = array('homepage','ccScripts');
		//signed in logic goes here

		$this->load->view('templates/header', $data);
		$this->load->view('pages/home', $data);
		$this->load->view('pages/ccScore', $data);
		$this->load->view('pages/homeBottom', $data);
		$this->load->view('templates/footer');
	}

	//claim page
	public function claim($claimID) {
		$data['headerTitle'] = 'View Claim - PatchWork';
		$data['pageType'] = 'claim';

		$data['claimInfo'] = $this->data_model->getClaim($claimID);
		$data['claimTags'] = $this->data_model->getClaimTags($claimID);
		
		//files needed
		$data['csFiles'] = array('general','ccStyles');
		$data['jsFiles'] = array('ccScripts');

		$this->load->view('templates/header', $data);
		$this->load->view('pages/ccTop', $data);
		$this->load->view('pages/ccScoreTop', $data);
		$this->load->view('pages/ccScore', $data);
		$this->load->view('pages/ccScoreBottom', $data);
		$this->load->view('pages/ccBottom', $data);
		$this->load->view('templates/footer');
	}

	//company page
	public function company($companyID) {
		$data['headerTitle'] = 'View Company - PatchWork';
		$data['pageType'] = 'company';
		
		//grab basic data
		$data['companyInfo'] = get_object_vars($this->data_model->getCompany($companyID));
		$data['companyClaims'] = $this->data_model->getCompanyClaims($companyID);
		$data['companyTags'] = $this->data_model->getCompanyTags($companyID);
		
		$data['csFiles'] = array('general','ccStyles');
		$data['jsFiles'] = array('ccScripts');
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/ccTop', $data);
		$this->load->view('pages/ccScoreTop', $data);
		$this->load->view('pages/ccScore', $data);
		$this->load->view('pages/ccScoreBottom', $data);
		$this->load->view('pages/highlowClaims', $data);
		$this->load->view('pages/ccBottom', $data);
		$this->load->view('templates/footer');
	}

	//tag page
	public function tag($tagID) {
		$data['headerTitle'] = 'View Tag - PatchWork';
		$data['pageType'] = 'tag';
		
		$data['tagInfo'] = get_object_vars($this->data_model->getTags($tagID));
		
		$data['csFiles'] = array('general');
		$data['jsFiles'] = array('');

		$this->load->view('templates/header', $data);
		$this->load->view('pages/ccTop', $data);
		$this->load->view('pages/ccBottom', $data);
		$this->load->view('templates/footer');
	}

	//profile page
	public function profile($userID) {
		//test if user is in system

		//grab basic data
		$data['userInfo'] = $this->data_model->basicProfileInfo($userID);

		$data['headerTitle'] = 'User Profile - Patchwork';
		$data['userName'] = $data['userInfo'][0]['Name'];
		//$data['pageTitle'] = $data['userInfo']['Name'];

		//files needed
		$data['csFiles'] = array('general','profile');
		$data['jsFiles'] = array('profile');

		$this->load->view('templates/header', $data);
		$this->load->view('pages/profile', $data);
		$this->load->view('templates/footer');
	}


	/*
	public function view($page = 'home') {
		if ( ! file_exists('ci_application/views/pages/'.$page.'.php')) {
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		//url helper
		$this->load->helper('url');

		// Capitalize the first letter
		$data['headerTitle'] = ucfirst($page) . ' - patchwork';
		$data['pageTitle'] = ucfirst($page);
		
		//load views
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'. $page, $data);
		$this->load->view('templates/footer', $data);
	}
	*/
}
