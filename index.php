<?php
require('./conf/setup.php');

$local = [
	'css'=>['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'],
];

$page = new \Athill\Utils\Page($local);
$_SESSION['flash']['info'][] = 'test';
// $base = new \Athill\Utils\TemplateBase();
// $h->pa($_SESSION['flash']);
// $h->pa($site['flash']);
// $base->messages();

// $h->pa($_ENV);



$page->end();
