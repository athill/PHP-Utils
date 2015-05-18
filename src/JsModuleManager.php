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
	
	public function getIncludes($moduleSettings, $includes=[]) {
		foreach ($this->modules['sequence'] as $id) {
			if (isset($moduleSettings[$id]) && $moduleSettings[$id])  {
				$module = $this->getModule($id);
				$includes = array_merge($includes, $this->addModuleFiles($module));
			}
		}
		return $includes;
	}	

	//// returns module config, based on id
	private function getModule($id) {
		if (isset($this->modules['modules'][$id])) {
			return $this->modules['modules'][$id];
		} else {
			throw new Exception('Undefined module '.$id);
		}
	}

	private function addModuleFiles($module, $includes=[]) {
		$filetypes = ['js', 'css'];
		$root = (isset($module['root'])) ? $module['root'] : '';
		foreach ($filetypes as $filetype) {
			$moduleType = $module[$filetype];
			if (isset($moduleType)) {
				$includes = array_merge($files, $this->addFiles($root, $moduleType));
			}
		}
		return $includes;	
	}

	private function addFiles($root, $files, $includes=[]) {
		if ($root != '') {
			$files = array_map(function($file) use($root) {
				return $root.$file;
			},
			$files);			
		} 
		return array_merge($includes, $files);
	}
}
