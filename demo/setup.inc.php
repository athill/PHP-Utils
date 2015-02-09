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

require_once($fileroot.'/vendor/autoload.php');

$setup = new Athill\Utils\Setup($basesettings);
echo $fileroot.'/vendor/autoload.php';
