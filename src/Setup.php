<?php namespace Athill\Utils;
class Setup {
	private $config = array();
	private $defaults = array();

	function __construct($basesettings) {
		
		$requireds = array('webroot');
		foreach ($requireds as $required) {
			if (!isset($basesettings[$required])) {
				throw new UnexpectedValueException('Required field '.$required.' missing from Setup args.');
			}
		}
		$this->defaults = array(
			'webroot'=>$basesettings['webroot'],

		);
		$this->config = array_merge_recursive($this->defaults, $basesettings);
		// $this->override()


	}

	function override($config) {
		$this->config = array_merge_recursive($this->config, $config);
	}
}