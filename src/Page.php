<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;
		//// local options	
		$site = array_merge_recursive($site, $options);
		////Template
		$templateClass = ucfirst($site['template']).'Template';
		$localTemplatePath = $site['classpath'].'/Templates/'.$templateClass.'.php';
		if (file_exists($localTemplatePath.'.php')) {
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