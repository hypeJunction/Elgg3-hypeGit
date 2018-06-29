<?php

namespace hypeJunction\Git;

use Elgg\Hook;
use hypeJunction\Fields\MetaField;

class SetupDownloadFields {

	/**
	 * Setup download object fields
	 *
	 * @param Hook $hook Hook
	 * @return void
	 */
	public function __invoke(Hook $hook) {

		$fields = $hook->getValue();
		/* @var $fields \hypeJunction\Fields\Collection */

		$fields->add('github:package_name', new MetaField([
			'type' => 'github/package_name',
			'is_profile_fields' => true,
			'priority' => 10,
		]));

		$fields->add('composer:package_name', new MetaField([
			'type' => 'composer/package_name',
			'is_profile_fields' => true,
			'is_edit_field' => false,
			'is_create_field' => false,
		]));
	}
}