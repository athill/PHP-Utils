<?php namespace Athill\Utils;

class TemplateBase {
	protected $js=array();
	protected $css = array();
	protected $jsModules = array();

	function __construct() {
		$this->start();
	}

	function start() {
		global $h, $site;
		//// jsModules
		$jsModuleFile = $site['fileroot'].'/jsmodules.php';
		$jsModules = array();
		if (file_exists($jsModuleFile)) {
			$jsModules = require($jsModuleFile);
		}
		$jsModule = new Athill\Utils\JsModule($jsModules);
		foreach ($jsModules as $module) {
			//// if it's been set on page/directory, leave that setting.
			if (!isset($site['jsModules'][$module])) {
				//// otherwise, if it's in the template jsModules set to true, otherwise set to false
				$site['jsModules'][$module] = in_array($module, $this->jsModules);
				
			}
		}		
		foreach ($this->jsModules as $module)
		foreach($site['jsModules'] as $module)
		$h->start();
		
		$h->head();
		$this->header();
	}

	function end() {

	}

	function head() {

	}


	function footer() {

	}

}

