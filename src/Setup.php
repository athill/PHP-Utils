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
			'classpath'=>'/classes',
			'template'=>'default',
			'view' => $self,
			'filename' => basename($self),
			'dir' => dirname($self),
			'pagetitle'=>null
		);
		
		//// logging
		$defaults['logger'] = new \Monolog\Logger("Main");
		$defaults['logger']->pushHandler(new \Monolog\Handler\StreamHandler($defaults['fileroot'].'/logs/main.log'));
		//// menu
		$menufile = $defaults['fileroot'].'/menu.json';
		// $defaults['logger']->info($menufile);
		$defaults['menu'] = array();
		if (file_exists($menufile)) {
			$defaults['menu'] = json_decode(file_get_contents($menufile), true);
		}		
		$defaults['meta'] = array(
		  'description' => $defaults['sitename'],
		  'keywords' => implode(',', explode(' ', $defaults['sitename'])),
		  'author' => isset($_SERVER['USERNAME']) ? $_SERVER['USERNAME'] : '',
		  'copyright' => date('Y').', '. $defaults['sitename'],
		  'icon'=>'',
		  'compatible'=>'IE=edge,chrome=1',
		  'viewport'=>'width=device-width',
		  'charset'=>'uft-8',
		  'title'=>null
		);
		$defaults['layout'] = array(
			'leftsidebar'=>[],
			'rightsidebar'=>[],
		);

		$utils = new \Athill\Utils\Utils();
		//// override base settings
		$defaults = $utils->extend($defaults, $this->basesettings);
		//// override directory settings
		$dirSettingsFile = $defaults['dir'].'/directorySettings.php';
		if (file_exists($dirSettingsFile)) {
			$dirSettings = require($dirSettingsFile);
			$defaults = $utils->extend($defaults, $dirSettings);
		}
		return $defaults;		
	}
}