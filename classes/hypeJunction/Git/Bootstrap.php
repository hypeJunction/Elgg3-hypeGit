<?php

namespace hypeJunction\Git;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function load(): void {
		$autoloader = dirname(__DIR__, 3) . '/autoloader.php';
		if (file_exists($autoloader)) {
			require_once $autoloader;
		}
	}

	public function init(): void {
		elgg_register_event_handler('fields', 'object:download', SetupDownloadFields::class);
		elgg_register_event_handler('modules', 'object:download', SetupDownloadModules::class);

		elgg_register_event_handler('create', 'object', SyncReleases::class);
		elgg_register_event_handler('update', 'object', SyncReleases::class);

		elgg_register_event_handler('cron', 'daily', SetupCron::class);
	}
}
