<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \hypeJunction\Downloads\Download) {
	return;
}

if (!$entity->{'manifest.xml'}) {
	return;
}

try {
	$manifest = new SimpleXMLElement($entity->{'manifest.xml'});
	$manifest = new \ElggXMLElement($manifest);
	$parser = new \ElggPluginManifest($manifest);

	$data = [
		'conflicts' => $parser->getConflicts(),
		'requires' => $parser->getRequires(),
		'suggests' => $parser->getSuggests(),
	];

	$deps = [];

	foreach ($data as $system => $items) {
		if (empty($items)) {
			continue;
		}

		$deps[$system] = [];

		foreach ($items as $item) {
			$type = elgg_extract('type', $item);
			$comparison = elgg_extract('comparison', $item);

			switch ($comparison) {
				case 'lt':
					$comparison = '<';
					break;
				case 'gt':
					$comparison = '>';
					break;
				case 'ge':
					$comparison = '>=';
					break;
				case 'le':
					$comparison = '<=';
					break;
			}

			$report = [];

			switch ($type) {
				case 'elgg_release':
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:Elgg');
					$report['version'] = "$comparison {$item['version']}";
					break;

				case 'php_version':
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:PhpVersion');
					$report['version'] = "$comparison {$item['version']}";
					break;

				case 'php_extension':
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:PhpExtension', [$item['name']]);
					if ($item['version']) {
						$report['version'] = "$comparison {$item['version']}";
					} else {
						$report['version'] = '';
					}
					break;

				case 'php_ini':
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:PhpIni', [$item['name']]);
					$report['version'] = "$comparison {$item['value']}";
					break;

				case 'plugin':
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:Plugin', [$item['name']]);
					$expected = $item['version'] ? "$comparison {$item['version']}" : elgg_echo('any');
					$report['version'] = $expected;
					break;

				case 'priority':
					$expected_priority = ucwords($item['priority']);
					$real_priority = ucwords($item['value']);
					$report['name'] = elgg_echo('ElggPlugin:Dependencies:Priority');
					$report['version'] = elgg_echo("ElggPlugin:Dependencies:Priority:$expected_priority", [$item['plugin']]);
					break;
			}

			$deps[$system][] = $report;
		}
	}
} catch (Exception $ex) {
	return;
}

if (empty($deps)) {
	return;
}

ob_start();
foreach ($deps as $system => $items) {
	?>

    <table class="elgg-table-alt downloads-deps__table">
        <thead>
        <th colspan="2">
            <h5><?= elgg_echo('ElggPlugin:Dependencies:' . ucwords($system)) ?></h5>
        </th>
        </thead>
        <tbody>
		<?php
		foreach ($items as $item) {
			?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['version'] ?></td>
            </tr>
			<?php
		}
		?>
        </tbody>
    </table>
	<?php
}
?>
<?php
$output = ob_get_clean();
if (!$output) {
	return;
}

echo elgg_view('post/module', [
	'title' => elgg_echo('downloads:dependencies'),
	'body' => $output,
	'collapsed' => false,
	'class' => 'download-deps__module',
]);