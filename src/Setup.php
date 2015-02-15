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
		$defaults = array(
			'webroot'=>'',
			'fileroot'=>'',
			'instance'=>'dev',
			'instances'=>array('dev', 'prd'),
			'sitename'=>'Hello World',
			'js'=>array(),
			'css'=>array(),
			'jsModules'=>array(),
			'classpath'=>'/classes',
			'template'=>'default',
			'view' => $_SERVER['PHP_SELF'],
			'filename' => basename($_SERVER['PHP_SELF']),
			'dir' => dirname($_SERVER['PHP_SELF']),


		);
		$defaults['pagetitle'] = $defaults['sitename'];
		$menufile = $defaults['fileroot'].'/menu.json';
		$defaults['menu'] = array();
		if (file_exists($menufile)) {
			$defaults['menu'] = json_decode($menufile);
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
		  'title'=>$defaults['sitename']
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