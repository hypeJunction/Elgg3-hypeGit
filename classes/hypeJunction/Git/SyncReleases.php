<?php

namespace hypeJunction\Git;

use Elgg\Event;
use hypeJunction\Downloads\Download;

class SyncReleases {

	/**
	 * Sync releases from Github
	 *
	 * @param Event $event Event
	 */
	public function __invoke(Event $event) {

		$entity = $event->getObject();

		if (!$entity instanceof Download) {
			return;
		}

		GithubEntities::instance()->syncPackageDetails($entity);
		GithubEntities::instance()->syncReleases($entity);
	}
}