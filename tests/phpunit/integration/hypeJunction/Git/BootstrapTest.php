<?php

namespace hypeJunction\Git;

use Elgg\IntegrationTestCase;

class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypegit';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPluginIsActive(): void {
		$plugin = \elgg_get_plugin_from_id('hypegit');
		$this->assertNotNull($plugin);
		$this->assertTrue($plugin->isActive());
	}

	public function testRoutesAreRegistered(): void {
		$routes = \_elgg_services()->routes;
		$this->assertNotNull($routes->get('github:webhook'));
	}

	public function testEventHandlersRegistered(): void {
		$events = \_elgg_services()->events;
		$this->assertTrue($events->hasHandler('create', 'object'));
		$this->assertTrue($events->hasHandler('update', 'object'));
		$this->assertTrue($events->hasHandler('cron', 'daily'));
	}
}
