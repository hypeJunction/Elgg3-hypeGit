<?php

$value = elgg_extract('value', $vars);
if (!$value) {
	return;
}

echo elgg_view('output/url', [
	'href' => "https://packagist.org/packages/$value",
	'target' => '_blank',
	'text' => $value,
]);