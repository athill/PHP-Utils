<?php
//// session
session_start();
//// tz
date_default_timezone_set('America/New_York');

//// need these
$webroot = '';
$fileroot = null;
//// determine fileroot based on host.
if (isset($_SERVER['HTTP_HOST'])) {
	//// remove port, if applicable
	$host = preg_replace('/([^:]+):\\d+/', '$1', $_SERVER['HTTP_HOST']);
	switch($host) {
		case 'demo.app':
			$fileroot = '/home/vagrant/Code/sites/PHP-Utils/demo';
			break;
		case 'localhost':
			$fileroot = '/home/athill/Code/Homestead/sites/PHP-Utils/demo';
			break;			
	}
}
$fileroot = $fileroot.$webroot;

//// autoloader
$loader = require_once($fileroot.'/vendor/autoload.php');

//// html engine. $h is a reserved variable of sorts.
$h = Athill\Utils\Html::singleton($webroot);

//// globally override things here. See Settings.php.
$basesettings = array(
	'webroot'=>$webroot,
	'fileroot'=>$fileroot,
);
//// set the wheels in motion
$setup = new Athill\Utils\Setup($basesettings);
//// $site is reserved as well. It determines various settings.
$site = $setup->getDefaults();