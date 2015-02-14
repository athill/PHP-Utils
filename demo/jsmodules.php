<?php
$bower = '/bower_components';
$localjs = '/js';
$localcss = '/css';

return array(
	'jquery' => array(
		'root'=>$bower.'/jquery/dist',
		'js'=>array('/jquery.min.js')
	),
	'bootstrap'=>array(
		'root'=>$bower.'/bootstrap/dist',
		'js'=>array('/js/bootstrap.min.js'),
		'css'=>array(
			'/css/bootstrap.min.css',
			'/css/bootstrap-theme.min.css'
		)
	)
);