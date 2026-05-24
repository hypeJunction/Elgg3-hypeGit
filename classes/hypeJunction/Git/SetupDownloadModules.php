<?php

namespace hypeJunction\Git;

use Elgg\Event;

/**
 * Event handler that registers Git-related modules on download entities
 */
class SetupDownloadModules {

	/**
	 * Add the download/manifest module to the sidebar
	 *
	 * @param Event $event Event
	 *
	 * @return array
	 */
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
