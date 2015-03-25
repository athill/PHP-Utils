<?php
require('./setup.inc.php');

$local = [];

$page = new \Athill\Utils\Page($local);
echo 'here';
$_SESSION['flash']['info'][] = 'test';
// $base = new \Athill\Utils\TemplateBase();
// $h->pa($_SESSION['flash']);
// $h->pa($site['flash']);
// $base->messages();

$h->pa($site['utils']['security']->authenticate(['username'=>'admin', 'password'=>'password']));


$page->end();
