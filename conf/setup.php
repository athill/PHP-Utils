<?php
//// session
session_start();
//// tz
date_default_timezone_set('America/New_York');

//// need these
$webroot = '';
$fileroot = dirname(dirname(__FILE__));
$instance = 'dev';

//// determine instance based on host
if (isset($_SERVER['HTTP_HOST'])) {
	//// remove port
	$host = preg_replace('/([^:]+):\\d+/', '$1', $_SERVER['HTTP_HOST']);
	switch ($host) {
		case 'demo.andyhill.us':
		case 'localhost':
			$instance = 'prd';
			break;
	}
}

// $fileroot = $fileroot.$webroot;

//// autoloader (composer update)
$loader = require_once($fileroot.'/vendor/autoload.php');

//// html engine. $h is a reserved variable of sorts.
$h = Athill\Utils\Html::singleton($webroot);

//// globally override things here. See Settings.php.
$basesettings = array(
	'webroot'=>$webroot,
	'fileroot'=>$fileroot,
	'instance'=>$instance,
);
//// set the wheels in motion
$setup = new Athill\Utils\Setup($basesettings);

//// $site is reserved as well. It determines various settings.
$site = $setup->getDefaults();

//// these depend on $site being set
$site['utils'] = $setup->getUtils();

