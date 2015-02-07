<?php  namespace Athill\Utils;
class JsModule {
	private $modules;

	/**
	 * Takes a json array of the form:
	 * { "module-name": {
	 *    "js": ["js-file1"], 
	 *    "css": ["cssfile1"]    ////js/css entries are optional
	 *   }, 
	 *   ...
	 * }
	 */
	
	function __construct($modules) {
		$this->modules = array(
			////Editor
			"editor" => array(
				"scripts" => array("/global/js/tinymce/jscripts/tiny_mce/tiny_mce.js", 
					"/global/js/tinymce/config.js"),
				"styles" => array()
			),
					////Tooltip
			"tooltip" => array( 
				"scripts" => array("/global/js/jquery-tooltip/lib/jquery.bgiframe.js",
								"/global/js/jquery-tooltip/lib/jquery.dimensions.js",
								"/global/js/jquery-tooltip/jquery.tooltip.js"),
				"styles" => array("/global/js/jquery-tooltip/jquery.tooltip.css",
							"/global/js/jquery-tooltip/demo/screen.css")
			),
			////Tree Table
			"treeTable" => array( 
				"scripts" => array("/global/js/treeTable/src/javascripts/jquery.treeTable.min.js"),
				"styles" => array("/global/js/treeTable/src/stylesheets/jquery.treeTable.css")
			),
			////High Charts
			"highcharts" => array(
				"scripts" => array("/global/js/Highcharts-4.0.3/js/highcharts.js"),
				"styles" => array("")
			),
			////Treeview
			"treeview" => array(
				"scripts" => array("/global/js/jquery.treeview/jquery.treeview.js", 
					"/global/js/jquery.treeview/lib/jquery.cookie.js"),
				"styles" => array("/global/js/jquery.treeview/jquery.treeview.css")
			),
			////ui
			"ui" => array(
				"scripts" => array("/global/js/jquery-ui/js/jquery-ui-1.10.4.custom.min.js"),
				"styles" => array("/global/js/jquery-ui/css/smoothness/jquery-ui-1.10.4.custom.min.css")
			),			
			////autogrow
			"autogrow" => array(
				"scripts" => array("/global/js/jquery.autogrowtextarea.js"),
				"styles" => array()
			),
			////angular
			"angular" => array(
				"scripts" => array("//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"),
				"styles" => array()
			),			

		);
	}
	public function modify ($mods,$action) {
		global $h;
		foreach ($mods as $module) {
			if (array_key_exists($module, $GLOBALS['jsModules'])) {
				
				$GLOBALS['jsModules'][$module] = $action;
			} else {
				$error = 'Undefined module in JsModule:modify()';
				throw  new Exception($error);
			} 
		}
	}
		
	
	public function add($mod) {
		$this->modify($mod, true);
		
	}
	
	public function remove($mod) {
		$this->modify($mod, false);
	}	

}


?>