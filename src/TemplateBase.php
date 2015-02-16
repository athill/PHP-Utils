<?php namespace Athill\Utils;

class TemplateBase {
	protected $js=array();
	protected $css = array();
	protected $jsModules = array();

	private $includes = array();
	private $menu;

	function __construct() {
		global $site;
		$this->menu = new MenuUtils();
		/////////
		//// jsModules
		/////////
		$jsModuleFile = $site['fileroot'].'/jsmodules.php';
		$jsModules = (file_exists($jsModuleFile)) ?
			require($jsModuleFile) :
			array();
		//// determine which modules to include
		foreach ($jsModules as $id => $data) {
			//// if it's been set on page/directory, leave that setting.
			if (!isset($site['jsModules'][$id])) {
				//// otherwise, if it's in the template jsModules set to true, otherwise set to false
				$site['jsModules'][$id] = in_array($id, $this->jsModules);
				
			}
		}
		//// add module files to includes
		$jsModuleManager = new \Athill\Utils\JsModuleManager($jsModules);
		foreach ($site['jsModules'] as $id => $include) {
			if ($include) {
				$module = $jsModuleManager->getModule($id);
				$this->addModuleFiles($module);
			}
		}
		//// template scripts and styles
		$this->includes = array_merge($this->includes, $this->js);
		$this->includes = array_merge($this->includes, $this->css);

		//// page/directory scripts and styles
		$this->includes = array_merge($this->includes, $site['js']);
		$this->includes = array_merge($this->includes, $site['css']);
	}

	private function addModuleFiles($module) {
		$filetypes = array('js', 'css');
		$root = (isset($module['root'])) ? $module['root'] : '';
		foreach ($filetypes as $filetype) {
			if (isset($module[$filetype])) {
				$this->addFiles($root, $module[$filetype]);
			}
		}	
	}

	private function addFiles($root, $files) {
		if ($root != '') {
			$files = array_map(function($file) use($root) {
				return $root.$file;
			},
			$files);			
		} 
		$this->includes = array_merge($this->includes, $files);
	}

	public function begin() {
		global $h, $site;
		$h->begin([
			'title'=>$site['meta']['title'],
			'includes'=>$this->includes,
			'headoptions'=>$site['meta']
		]);
		$this->beginRender();
		$this->heading();
		$this->beginLayout();
	}

	public function end() {
		global $h;
		$this->endLayout();
		$this->footer();
		$this->endRender();
		$h->end();

	}

	protected function sidebar($id, $items) {
		global $h;
		$h->oaside('id="'.$id.'" class="sidebar"');
		foreach ($items as $item) {
			switch ($item['type']) {
				case 'content':
					$h->section($item['content'], 'class="sidebar-section"');
					break;
				case 'menu':

					break;
			}
		}
		$h->caside('./'.$id);
	}

	protected function renderMenu($options=array()) {

	}

	protected function beginRender() {}

	protected function heading() {}

	protected function topMenu() {}

	protected function breadcrumbs() {}

	protected function beginLayout() {}

	protected function leftSidebar() {}

	protected function rightSidebar() {}

	protected function endLayout() {}	

	protected function footer() {}

	protected function endRender() {}

}