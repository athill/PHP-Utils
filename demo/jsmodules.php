<?php
$bower = '/bower_components';
$localjs = '/js';
$localcss = '/css';

return array(
	'jquery' => array(
		'js'=>array($bower.'/jquery/dist/jquery.min.js')
	),
	'bootstrap'=>array(
		'js'=>array($bower.'/bootstrap/dist/js/bootstrap.min.js'),
		'css'=>array($bower.'/boostrap/dist/css/bootstrap.min.css',
			$bower.'/bootstrap/dist/css/bootstrap-theme.min.css'
		)
	)
);