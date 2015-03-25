<?php  namespace Athill\Utils;
class Security {

	function __construct() {
		
	}

	public function authenticate($credentials=[]) {
		global $h;
		$validation = $this->validate($credentials);
		$valid = !isset($validation['error']);
		$data = ($valid) ? 
			$this->getUserData($credentials['username']) : 
			$validation['error'];
		return [
			'valid'=>$valid,
			'data'=>$data
		];
	}

	protected function validate($credentials) {
		$data = [];
		if (!in_array($credentials['username'], ['user', 'admin']) 
				|| $credentials['password'] != 'password') {
			$data['error'] = [
				'message'=>'Bad username or password'
			];
		}
		return $data;
	}

	//// this shpuld be a call to the database or what have you
	protected function getUserData($user) {
		switch ($user) {
			case 'user':
				return $this->getDummyUserData($user, ['users'], 'user@demo.com', 'user name');
				break;
			case 'admin':
				return $this->getDummyUserData($user, ['users', 'admin'], 'admin@demo.com', 'ad min');
				break;			
			default:
				throw new Exception('Only valid users allowed in this method');
		}
	}

	private function getDummyUserData($username, $groups, $email, $display) {
		$names = explode(' ', $display);
		return [
			'user'=>$username,
			'groups'=>$groups,
			'email'=>$email,
			'display'=>$display,
			'fname'=>$names[0],
			'lname'=>$names[1],
		];
	}
	
	// function __construct($groups='', $user = ''){
	// 	if($groups == '' && isset($_SESSION['adsgroups'])){
	// 		$this->groups = $_SESSION['adsgroups'];
	// 	} else if(is_array($groups)) {
	// 		$this->groups = $groups;
	// 	}
	// 	if($user == '' && isset($_SESSION['user'])){
	// 		$this->user = $_SESSION['user'];
	// 	} else {
	// 		$this->user = $user;	
	// 	}
	// }
	
	function isAuthorized($items) {
		$valid = false;
		foreach ($items as $item) {
			list($has, $test) = ($item[0] == '!') ? 
				array(false, substr($item, 1)) :
				array(true, $item);
			if ($test == $this->user || $this->hasGroup($test)) {
				$valid = $has;	
			}
		}
		return $valid;
	}
	
	// 'IU-WH-USSS_Developers', 'fake-IU-UITS-MANAGED-BL-CURRENTLYENROLLED'
	
	
	function hasGroup($group){
		return in_array($group, $this->groups);
	}
	
	
	
	
	
	
}