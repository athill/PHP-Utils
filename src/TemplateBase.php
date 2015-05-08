<?php namespace Athill\Utils;

class TemplateBase {
	//// override these
	protected $jsModules = [];	
	protected $js = [];
	protected $css = [];

	
	private $includes = array();
	
	protected $flashTypes = ['success', 'info', 'warning', 'danger'];
	// protected $menuUtils;
	protected $breadcrumbs;

	function __construct() {
		global $site;
		//$site['utils']['menu'] = new MenuUtils();
		

		//// TODO: allow formatting (sprintf) to determine e.g., sitename - pagetitle		
		if (is_null($site['pagetitle'])) {
			$breadcrumbs = $this->getBreadcrumbs();
			$lastcrumb = $breadcrumbs[count($this->breadcrumbs) - 1];
			$pagetitle = $lastcrumb['display'];
			$site['pagetitle'] = $pagetitle;
		}
		if (is_null($site['meta']['title'])) {
			$format = $site['meta']['titleformat'][0];
			$args = array_slice($site['meta']['titleformat'], 1);
			$args = array_map(function($arg) use ($site) {
				return $site[$arg];
			},
			$args);
			$site['meta']['title'] = vsprintf($format, $args);
		}		
		/////////
		//// jsModules
		/////////
		$jsModuleFile = $site['fileroot'].'/jsmodules.php';
		$jsModules = (file_exists($jsModuleFile)) ?
			require($jsModuleFile) :
			['sequence'=>[]];
		//// determine which modules to include
		foreach ($jsModules['sequence'] as $id) {
			//// if it's been set on page/directory, leave that setting.
			if (!isset($site['jsModules'][$id])) {
				//// otherwise, if it's in the template jsModules set to true, otherwise set to false
				$site['jsModules'][$id] = in_array($id, $this->jsModules);
				
			}
		}
		//// add module files to includes
		$jsModuleManager = new \Athill\Utils\JsModuleManager($jsModules);


		//// TODO: Move this and the methods it uses to the manager
		foreach ($jsModules['sequence'] as $id) {
			if (isset($site['jsModules'][$id]) && $site['jsModules'][$id])  {
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

		//// flash
		if (!isset($_SESSION['flash'])) {
			$this->clearFlash();
		}
		$site['flash'] = $_SESSION['flash'];
		$this->clearFlash();
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

	protected function getBreadcrumbs() {
		global $site;
		if (is_null($this->breadcrumbs)) {
			$this->breadcrumbs = $site['utils']['menu']->getBreadcrumbs();	
		}
		
		return $this->breadcrumbs;
		
	}

	protected function clearFlash() {
		$_SESSION['flash'] = [];
		foreach ($this->flashTypes as $type) {
			$_SESSION['flash'][$type] = [];
		}
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
		global $site, $h;
		$h->oaside('id="'.$id.'" class="sidebar"');
		foreach ($items as $item) {
			switch ($item['type']) {
				case 'content':
					$h->section($item['content'], 'class="sidebar-section"');
					break;
				case 'menu':
					$start = (isset($item['start'])) ? 
						$item['start'] : 
						dirname($site['view']).'/';
					$h->onav('id="sidebar-menu"');
					$site['utils']['menu']->renderMenu([
						'start'=>$start
					]);
					$h->cnav('/#sidebar-menu');
					break;
			}
		}
		$h->caside('./'.$id);
	}

	public function messages() {
		global $site, $h;
		foreach ($this->flashTypes as $type) {
			//// TODO: this should be handled before now
			if (!isset($site['flash'][$type])) {
				$site['flash'][$type] = [];
			}
			$body = $site['flash'][$type];
			if (count($body) != 0) {
				$this->panel([
					'heading'=>ucfirst($type),
					'type'=>$type,
					'body'=>$body
				]);				
			}

		}
		
	}

	protected function panel($opts=[]) {
		global $h;
		$defaults = [
			'heading'=>'',
			'footer'=>'',
			'type'=>'default',
			'body'=>''
		];
		$opts = $h->extend($defaults, $opts);
		$paneltype = 'panel-'.$opts['type'];
		$h->odiv(['class'=>'panel '.$paneltype]);
		$heading = $opts['heading'];
		//// heading
		if ($heading != '') {
			$h->div($h->rtn('h3', [$heading, ['class'=>'panel-title']]), ['class'=>'panel-heading']);
		}
		$body = $opts['body'];
		if (is_array($body)) {
			$body = $h->rtn('ul', [$body]);
		}
		$h->div($body, ['class'=>'panel-body']);
		// $h->pa($opts);
		$footer = $opts['footer'];
		if (!$footer != '') {
			$h->div($footer, ['class'=>'panel-footer']);
		}
		$h->cdiv('/.'.$paneltype);
	}

	protected function renderMenu($options=array()) {

	}

	protected function beginRender() {}

	protected function heading() {}


	protected function breadcrumbs($opts=[]) {
		global $h;
		$defaults = [
			'navatts'=>[]
		];
		$opts = $h->extend($defaults, $opts);
		$h->onav($opts['navatts']);
		$breadcrumbs = $this->getBreadcrumbs();
		$lastbc = count($breadcrumbs) - 1;
		$crumbs = [];
		foreach ($breadcrumbs as $i => $breadcrumb){
			if ($i == $lastbc) {
				$crumbs[] = ($breadcrumb['display']);
			} else {
				$crumbs[] = ($h->rtn('a', [$breadcrumb['href'], $breadcrumb['display']]));
			}
		}
		$h->ul($crumbs);
		$h->cnav('/.breadcrumbs');
	}

	protected function menu($opts = []) {
		global $h, $site;
		$defaults = [
			'navatts'=>[],
			'ulatts'=>[],
			'depth'=>-1
		];
		$opts = $h->extend($defaults, $opts);
		$h->onav($opts['navatts']);
		$site['utils']['menu']->renderMenu([ 
			'rootatts'=>$opts['ulatts'],
			'depth'=>$opts['depth']
		]);
		$h->cnav();		
	}	

	protected function beginLayout() {}

	protected function leftSidebar() {}

	protected function rightSidebar() {}

	protected function endLayout() {}	

	protected function footer() {}

	protected function endRender() {}

}