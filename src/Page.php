<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;
		//// local options	
		$site = $site['utils']['utils']->extend($site, $options);
		////Template
		$templateClass = $site['objects']['template'];
		$site['utils']['template'] = new $templateClass();
		$site['utils']['template']->begin();		
	}

	public function end() {
		global $site;
		$site['utils']['template']->end();
	}
}
?>