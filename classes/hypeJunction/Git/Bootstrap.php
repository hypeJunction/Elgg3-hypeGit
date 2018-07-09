<?php

namespace hypeJunction\Git;

use Elgg\Includer;
use Elgg\PluginBootstrap;
use Github\Client;
use hypeJunction\Downloads\SyncReleaseAccess;

class Bootstrap extends PluginBootstrap {

	/**
	 * Get plugin root
	 * @return string
	 */
	protected function getRoot() {
		return $this->plugin->getPath();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		Includer::requireFileOnce($this->getRoot() . '/autoloader.php');
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		elgg_register_plugin_hook_handler('fields', 'object:download', SetupDownloadFields::class);
		elgg_register_plugin_hook_handler('modules', 'object:download', SetupDownloadModules::class);

		elgg_register_event_handler('create', 'object', SyncReleases::class);
		elgg_register_event_handler('update', 'object', SyncReleases::class);

		elgg_register_plugin_hook_handler('cron', 'daily', SetupCron::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {

	}

}