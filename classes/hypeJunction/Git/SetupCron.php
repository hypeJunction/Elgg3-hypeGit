<?php

namespace hypeJunction\Git;

class SetupCron {

	/**
	 * Sync all packages on daily basis
	 */
	public function __invoke() {
		elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function() {
			$downloads = elgg_get_entities([
				'types' => 'object',
				'limit' => 0,
				'batch' => true,
				'metadata_names' => [
					'github:package_name',
				],
			]);

			foreach ($downloads as $download) {
				$download->save();
			}
		});
	}
}