<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";

	$handler = new \App\Controller\Handler();
	$channelid = \App\Model\Utilities::data_filter($_GET['channelid']);

	$handler->render([
		'tag'     => 'channel',
		'title'   => 'Channel',
		'user'    => $handler->user->data,
		'channel' => $handler->getChannelData($channelid),
		'error'   => $handler->last_error
	]);
