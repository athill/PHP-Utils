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
		if (isset($this->modules['modules'][$id])) {
			return $this->modules['modules'][$id];
		} else {
			throw new Exception('Undefined module '.$id);
		}
	}	
}
