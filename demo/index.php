<?php
require_once('setup.inc.php');
$local = [
	'layout'=>[
		'leftsidebar': [['type'=>'content', 'content'=>'left side bar']],
		'rightsidebar': [['type'=>'content', 'content'=>'right side bar']],
	]
];

$page = new \Athill\Utils\Page();

$h->p('content');

$page->end();