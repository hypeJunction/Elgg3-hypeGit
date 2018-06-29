<?php

return [
	'github.client' => \DI\object(\Github\Client::class),
	'github.query' => \DI\object(\hypeJunction\Git\GithubQuery::class)
		->constructor(\DI\get('github.client')),
	'github.entities' => \DI\object(\hypeJunction\Git\GithubEntities::class)
		->constructor(\DI\get('github.query')),
];
