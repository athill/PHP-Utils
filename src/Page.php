<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;
		//// local options	
		print_r($site['rightsidebar']);
		$site = array_merge_recursive($site, $options);
		print_r($site['rightsidebar']);
		////Template
		$templateClass = ucfirst($site['template']).'Template';
		$localTemplatePath = $site['fileroot'].$site['classpath'].'/Templates/'.$templateClass.'.php';
		if (file_exists($localTemplatePath)) {
			require($localTemplatePath);
			$this->template = new $templateClass();
		} else {
			$templateClass = "Athill\\Utils\\Templates\\".$templateClass;
			$this->template = new $templateClass();
		}
		$this->template->begin();
	}

	public function end() {
		$this->template->end();
	}
}
?>