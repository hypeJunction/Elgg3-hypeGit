<?php

namespace hypeJunction\Git;

use Elgg\Hook;

class SetupDownloadModules {

	public function __invoke(Hook $hook) {

		$modules = $hook->getValue();

		$modules['download/manifest'] = [
			'enabled' => true,
			'position' => 'sidebar',
			'priority' => 200,
		];

		return $modules;
	}
}