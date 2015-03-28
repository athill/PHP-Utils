<?php
require('./setup.inc.php');

$local = [];

$page = new \Athill\Utils\Page($local);
$_SESSION['flash']['info'][] = 'test';
// $base = new \Athill\Utils\TemplateBase();
// $h->pa($_SESSION['flash']);
// $h->pa($site['flash']);
// $base->messages();

// $h->pa($site['utils']['security']->authenticate(['username'=>'admin', 'password'=>'password']));
$data = $site['utils']['utils']->readJson('../PHP-Utils-Demo/menu.json');

$view = '/nested/index.php';

$menu = new \Athill\Utils\MenuUtils($view, $data);

// $h->pa($menu);

$menu->ls(['depth'=>-1]);


$page->end();
