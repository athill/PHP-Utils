<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;
		//// local options	
		$site = array_merge_recursive($site, $options);
		////Template
		$templateClass = $site['objects']['template'];
		$this->template = new $templateClass();
		$this->template->begin();
	}

	public function end() {
		$this->template->end();
	}
}
?>