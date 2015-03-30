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

$config = [
	'defs' => [
		'login'=>[
			'label'=>'Login'
		],
		'password'=>[
			'fieldtype'=>'password',
			'label'=>'Password'
		],
		'submit'=>[
			'fieldtype'=>'submit',
			'value'=>'Submit'
		],
		'clear'=>[
			'fieldtype'=>'button',
			'content'=>'Clear'
		]
	],
	'layout' => ['login', 'password'],
	'buttons' => ['submit', 'clear']
];


$form = new \Athill\Utils\Uft\FormHorizontal($config);
$form->render(['leftcolwidth'=>1]);

$page->end();
