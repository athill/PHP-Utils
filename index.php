<?php
require('./setup.inc.php');

$local = [
	'css'=>['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css']
];

$page = new \Athill\Utils\Page($local);
$_SESSION['flash']['info'][] = 'test';
// $base = new \Athill\Utils\TemplateBase();
// $h->pa($_SESSION['flash']);
// $h->pa($site['flash']);
// $base->messages();

// $h->pa($site['utils']['security']->authenticate(['username'=>'admin', 'password'=>'password']));
// $view = '/nested/nest2/nest2.2/nest2.2.1.php';
// echo $site['view'];
// $struct = $site['utils']['utils']->readJson('menu.json');
// // $h->pa($struct);
// $menu = new \Athill\Utils\MenuUtils($view);
// $menu->renderMenu();

$page->end();
