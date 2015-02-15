<?php  namespace Athill\Utils;

class JsModuleManager {
	private $modules;

	/**
	 * Takes a json array of the form:
	 * { "module-name": {
	 *    "root": 'path-to-files-root'	////optional
	 *    "js": ["js-file1", ...], 
	 *    "css": ["cssfile1", ...]    ////js/css entries are optional
	 *   }, 
	 *   ...
	 * }
	 */
	
	function __construct($modules) {
		$this->modules = $modules;
	}
	
	
	public function getModule($id) {
		if (isset($this->modules[$id])) {
			return $this->modules[$id];
		} else {
			throw new Exception('Undefined module '.$id);
		}
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