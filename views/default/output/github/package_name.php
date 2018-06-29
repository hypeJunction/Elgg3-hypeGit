<?php

$value = elgg_extract('value', $vars);
if (!$value) {
	return;
}

echo elgg_view('output/url', [
	'href' => "https://github.com/$value",
	'target' => '_blank',
	'text' => $value,
]);