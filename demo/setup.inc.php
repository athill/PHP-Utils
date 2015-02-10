<?php
//// tz
date_default_timezone_set('America/New_York');
/**************
Call session_start at TOP of page
$SID = session_id();
if(empty($SID)) session_start() or exit(basename(__FILE__).'(): Could not start session'); 
**********************************/
//// Start session if not started
if (!isset($_SESSION)) {
	session_start();	
}

//// need these
$webroot = '';
$fileroot = '/home/athill/Code/github/PHP-Utils/demo';

$basesettings = array(
	'webroot'=>$webroot,
	'fileroot'=>$fileroot
);

//// autoloader

$loader = require_once($fileroot.'/vendor/autoload.php');
echo $fileroot.'/vendor/autoload.php';
print_r($loader);

$h = Athill\Utils\Html::singleton();

$h->p('hello world');
$setup = new Athill\Utils\Setup($basesettings);
//new Athill\Generator\Xml();

echo 'setup complete';