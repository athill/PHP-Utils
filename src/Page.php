<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site;

		////Local Settings override global and directory
		// if (isset($options['jsModules'])) {
		// 	$options['jsModules'] = array_merge($site['jsModules'], $options['jsModules']) or die("^^^!!");
		// }
		// if (isset($options)) $site = array_merge($site, $options) or die("^^^");	

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