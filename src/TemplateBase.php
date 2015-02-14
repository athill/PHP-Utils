<?php namespace Athill\Utils;

class TemplateBase {
	protected $js=array();
	protected $css = array();
	protected $jsModules = array();

	private $includes = array();

	function __construct() {
		global $site;
		//// jsModules
		$jsModuleFile = $site['fileroot'].'/jsmodules.php';
		$jsModules = array();
		if (file_exists($jsModuleFile)) {
			$jsModules = require($jsModuleFile);
		}
		$jsModule = new \Athill\Utils\JsModule($jsModules);
		foreach ($jsModules as $module => $val) {
			//// if it's been set on page/directory, leave that setting.
			if (!isset($site['jsModules'][$module])) {
				//// otherwise, if it's in the template jsModules set to true, otherwise set to false
				$site['jsModules'][$module] = in_array($module, $this->jsModules);
				
			}
		}
		//// add module files to includes
		foreach ($site['jsModules'] as $module => $include) {
			if ($include) {
				$files = $jsModule->getModule($module);
				if (isset($files['js'])) {
					$this->includes = array_merge($this->includes, $files['js']);
				}
				if (isset($files['css'])) {
					$this->includes = array_merge($this->includes, $files['css']);
				}				
			}
		}
		//// template scripts and styles
		$this->includes = array_merge($this->includes, $this->js);
		$this->includes = array_merge($this->includes, $this->css);

		//// page/directory scripts and styles
		$this->includes = array_merge($this->includes, $site['js']);
		$this->includes = array_merge($this->includes, $site['css']);

		$this->start();
	}

	function start() {
		global $h, $site;
		$h->start();
		
		$h->head($site['pagetitle'], $includes, $site['meta']);
		$this->heading();
	}

	function end() {
		global $h;
		$this->footer();
		$h->chtml();

	}

	function head() {

	}

	function heading() {

	}


	function footer() {

	}

}