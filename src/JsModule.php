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
		$this->modules = $modules;
	}
	
	

	public function modify ($mods,$action) {
		global $site;
		foreach ($mods as $module) {
			if (array_key_exists($module, $site['jsModules'])) {
				
				$site['jsModules'][$module] = $action;
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