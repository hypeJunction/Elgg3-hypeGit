<?php

return [
	'plugin' => [
		'version' => '5.0.0',
	],

	'bootstrap' => \hypeJunction\Git\Bootstrap::class,

	'routes' => [
		'github:webhook' => [
			'path' => '/github/webhook/{guid}',
			'controller' => \hypeJunction\Git\DigestWebhook::class,
			'walled' => false,
		],
	],
];
