<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$handler->render([
		'tag'      => 'catalog',
		'title'    => 'Catalog',
		'user'     => $handler->user->data,
		'channels' => $handler->logic->getChannels()
	]);
	