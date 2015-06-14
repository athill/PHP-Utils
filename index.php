<?php
require('./conf/setup.php');

$local = [
	'css'=>['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'],
];

$page = new \Athill\Utils\Page($local);
$h->p('Page Content');


$page->end();
