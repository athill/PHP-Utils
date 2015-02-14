<?php namespace Athill\Utils;
class Setup {
	private $config = array();
	private $defaults = array();

	function __construct($basesettings) {
		
		$requireds = array('webroot', 'fileroot');
		foreach ($requireds as $required) {
			if (!isset($basesettings[$required])) {
				throw new UnexpectedValueException('Required field '.$required.' missing from Setup args.');
			}
		}
		$this->setDefaults();
		print_r($this->defaults);
		$this->config = array_merge_recursive($this->defaults, $basesettings);
		
		// $this->override()


	}

	protected function setDefaults() {
		$defaults = array(
			'webroot'=>'',
			'fileroot'=>'',
			'instance'=>'dev',
			'instances'=>array('dev', 'prd'),
			'sitename'=>'Hello World',
			'js'=>array(),
			'css'=>array(),
			'templatelocation'=>'/classes/Templates',
			'template'=>'default',
			'view' => $_SERVER['PHP_SELF'],
			'filename' => basename($_SERVER['PHP_SELF']),

		);
		$defaults['pagetitle'] = $defaults['sitename'];


		$defaults['meta'] = array(
		  'description' => $defaults['sitename'],
		  'keywords' => implode(',', explode(' ', $defaults['sitename'])),
		  'author' => isset($_SERVER['USERNAME']) ? $_SERVER['USERNAME'] : '',
		  'copyright' => date('Y'). $defaults['sitename'],
		  'icon'=>'',
		  'compatible'=>'IE=edge,chrome=1',
		  'viewport'=>'width=device-width',
		  'charset'=>'uft-8',
		  'title'=>$defaults['sitename']
		);
		$defaults['layout'] = array(
			'leftsidebar'=>'',
			'rightsidebar'=>'',
		);
		$this->defaults = $defaults;
	}

	function override($config) {
		$this->config = array_merge_recursive($this->config, $config);
	}


}