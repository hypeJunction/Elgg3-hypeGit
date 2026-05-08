<?php

return [
	'github.client' => \DI\create(\Github\Client::class),
	'github.query' => \DI\create(\hypeJunction\Git\GithubQuery::class)
		->constructor(\DI\get('github.client')),
	'github.entities' => \DI\create(\hypeJunction\Git\GithubEntities::class)
		->constructor(\DI\get('github.query')),
];
