<?php

namespace hypeJunction\Git;

use Elgg\Event;

class SetupDownloadModules {

	public function __invoke(Event $event) {

		$modules = $event->getValue();

		$modules['download/manifest'] = [
			'enabled' => true,
			'position' => 'sidebar',
			'priority' => 200,
		];

		return $modules;
	}
}