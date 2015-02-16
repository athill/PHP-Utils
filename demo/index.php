<?php
require_once('setup.inc.php');
$local = [
	'layout'=>[
		'leftsidebar'=> [['type'=>'content', 'content'=>'left side bar']],
		'rightsidebar'=> [['type'=>'content', 'content'=>'right side bar']],
	]
];

$page = new \Athill\Utils\Page($local);

$h->p('content');
$h->a('http://andyhill.us', 'andyhill.us');

$menu = new \Athill\Utils\Menu('/nested/nest2/nest2.2/nest2.2.1.php');
$bc = $menu->getBreadcrumbs();
$h->pa($bc);

$menu->renderMenu();

$page->end();