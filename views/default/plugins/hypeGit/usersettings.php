<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity ElggPlugin */

$user = elgg_get_page_owner_entity();

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('github:usersettings:token'),
	'name' => 'params[github-token]',
	'value' => $entity->getUserSetting('github-token', $user->guid),
]);
