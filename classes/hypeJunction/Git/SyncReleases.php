<?php

namespace hypeJunction\Git;

use Elgg\Event;
use hypeJunction\Downloads\Download;

/**
 * Event handler that syncs Github releases for download entities
 */
class SyncReleases {

	/**
	 * Sync releases from Github
	 *
	 * @param Event $event Event
	 *
	 * @return void
	 */
	public function __invoke(Event $event) {

		$entity = $event->getObject();

		if (!$entity instanceof Download) {
			return;
		}

		GithubEntities::instance()->syncPackageDetails($entity);
		GithubEntities::instance()->syncReleases($entity);
		GithubEntities::instance()->setupWebhook($entity);
	}
}
