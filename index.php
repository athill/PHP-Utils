<?php
require('./setup.inc.php');

$local = ['css'=>['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css']];

$page = new \Athill\Utils\Page($local);
$_SESSION['flash']['info'][] = 'test';
// $base = new \Athill\Utils\TemplateBase();
// $h->pa($_SESSION['flash']);
// $h->pa($site['flash']);
// $base->messages();

// $h->pa($site['utils']['security']->authenticate(['username'=>'admin', 'password'=>'password']));

$defs = [
	'login'=>[
		'label'=>'Login'
	],
	'password'=>[
		'fieldtype'=>'password',
		'label'=>'Password'
	],
];

$layout = ['login', 'password', ];


// $fh = new \Athill\Utils\FieldHandler($defs);
// $fh->renderLabel('login');
// $fh->renderField('login');

$form = new \Athill\Utils\Uft\FormHorizontal($defs, $layout);
$form->render(['leftcolwidth'=>3]);

$page->end();
