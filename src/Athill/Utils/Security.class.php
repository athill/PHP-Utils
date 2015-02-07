<?php  namespace Athill\Utils;
class Security {
	var $groups=array();
	var $user = '';
	
	function __construct($groups='', $user = ''){
		if($groups == '' && isset($_SESSION['adsgroups'])){
			$this->groups = $_SESSION['adsgroups'];
		} else if(is_array($groups)) {
			$this->groups = $groups;
		}
		if($user == '' && isset($_SESSION['user'])){
			$this->user = $_SESSION['user'];
		} else {
			$this->user = $user;	
		}
	}
	
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