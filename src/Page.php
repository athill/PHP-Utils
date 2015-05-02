<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;
		////Template
		$templateClass = $site['objects']['template'];
		$site['utils']['template'] = new $templateClass();
		//// local options	
		$site = $site['utils']['utils']->extend($site, $options);
		$site['utils']['template']->begin();
	}

	public function end() {
		global $site;
		$site['utils']['template']->end();
	}
}
?>