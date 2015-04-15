<?php namespace Athill\Utils;
class Setup {
	private $basesettings = array();

	function __construct($basesettings) {
		
		$requireds = array('webroot', 'fileroot');
		foreach ($requireds as $required) {
			if (!isset($basesettings[$required])) {
				throw new UnexpectedValueException('Required field '.$required.' missing from Setup args.');
			}
		}
		$this->basesettings = $basesettings;
	}

	public function getDefaults() {
		$self = $_SERVER['PHP_SELF'];
		$defaults = array(
			'webroot'=>$this->basesettings['webroot'],
			'fileroot'=>$this->basesettings['fileroot'],
			'instance'=>'dev',
			'instances'=>array('dev', 'prd'),
			'sitename'=>'Hello World',
			'js'=>array(),
			'css'=>array(),
			'jsModules'=>array(),
			'view' => $self,
			'filename' => basename($self),
			'dir' => dirname($self),
			'pagetitle'=>null,
			'objects'=>[
				'auth'=>'\Athill\Utils\Auth',
				'security'=>'\Athill\Utils\Security',
				'template'=>'\Athill\Utils\Templates\DefaultTemplate',
				'menu'=>'\Athill\Utils\MenuUtils',
				'utils'=>'\Athill\Utils\Utils'
			]			
		);
		
		//// logging
		if (isset($this->basesettings['logger'])) {
			$defaults['logger'] = $this->basesettings['logger'];
		} else {
			$defaults['logger'] = new \Monolog\Logger("Main");
			$defaults['logger']->pushHandler(new \Monolog\Handler\StreamHandler($defaults['fileroot'].'/logs/main.log'));
		}
		//// menu
		$menufile = $defaults['fileroot'].'/menu.json';
		$defaults['menu'] = array();
		if (file_exists($menufile)) {
			$defaults['menu'] = json_decode(file_get_contents($menufile), true);
		}
		//// meta info to be passed to head tag
		$defaults['meta'] = array(
		  'description' => $defaults['sitename'],
		  'keywords' => implode(',', explode(' ', $defaults['sitename'])),
		  'author' => isset($_SERVER['USERNAME']) ? $_SERVER['USERNAME'] : '',
		  'copyright' => date('Y').', '. $defaults['sitename'],
		  'icon'=>'',
		  'compatible'=>'IE=edge,chrome=1',
		  'viewport'=>'width=device-width',
		  'charset'=>'utf-8',
		  'title'=>null,
		  'titleformat'=>null,

		);

		if (is_null($defaults['meta']['titleformat'])) {
			$defaults['meta']['titleformat'] = ['%s - %s', 'sitename', 'pagetitle'];
		}
		//// page layout
		$defaults['layout'] = array(
			'leftsidebar'=>[],
			'rightsidebar'=>[],
		);

		$utils = new $defaults['objects']['utils']();
		//// override base settings
		$defaults = $utils->extend($defaults, $this->basesettings);
		//// override directory settings
		$dirSettingsFile = $defaults['fileroot'].$defaults['dir'].'/directorySettings.php';
		if (file_exists($dirSettingsFile)) {
			$dirSettings = require($dirSettingsFile);
			$defaults['jsModules'] = array_merge($defaults['jsModules'], $dirSettings['jsModules']);
			$defaults = $utils->extend($defaults, $dirSettings);
		}

		return $defaults;		
	}

	//// site should be set by now


	public function getUtils() {
		global $site;
		//// create global objects
		$utils = [
			'security' => new $site['objects']['security'](),
			'utils'=> new \Athill\Utils\Utils(),
			'template'=>null, 	////
			'menu'=>new $site['objects']['menu']() 		//// MenuUtils
		];
		return $utils;
	}
}