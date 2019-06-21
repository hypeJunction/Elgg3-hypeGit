<?php

namespace hypeJunction\Git;

use Elgg\Di\ServiceFacade;
use hypeJunction\Downloads\Download;
use hypeJunction\Downloads\Release;
use hypeJunction\Post\River;
use Michelf\MarkdownExtra;

class GithubEntities {

	use ServiceFacade;

	/**
	 * @var GithubQuery
	 */
	protected $query;

	/**
	 * Constructor
	 *
	 * @param GithubQuery $query Query
	 */
	public function __construct(GithubQuery $query) {
		$this->query = $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function name() {
		return 'github.entities';
	}

	/**
	 * Sync package details from github
	 *
	 * @param Download $download Download object
	 *
	 * @return mixed
	 */
	public function syncPackageDetails(Download $download) {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () use ($download) {
			$name = $download->{'github:package_name'};
			if (!$name) {
				return;
			}

			$owner = $download->getOwnerEntity();
			if (!$owner instanceof \ElggUser) {
				unset($download->{'github:package_name'});
				unset($download->{'composer:package_name'});

				return;
			}

			$this->query->authenticate($download->getOwnerEntity());

			$package = $this->query->getPackage($name);
			if (!$package) {
				return;
			}

			if (!$download->title) {
				$download->title = $package['name'];
			}

			if (!$download->excerpt) {
				$download->excerpt = $package['description'];
			}

			if (!$download->description) {
				foreach (['README.md', 'readme.md', 'readme', 'readme.txt', 'README.txt'] as $path) {
					$readme = $this->query->getFile($name, $path);
					if ($readme) {
						$description = MarkdownExtra::defaultTransform($readme);
						$download->description = $description;
						break;
					}
				}
			}

			if (!$download->changelog) {
				foreach (['CHANGELOG.md', 'changelog.md', 'changelog'] as $path) {
					$changelog = $this->query->getFile($name, $path);
					if ($changelog) {
						$changelog = htmlspecialchars($changelog);
						$changelog = MarkdownExtra::defaultTransform($changelog);
						$download->changelog = $changelog;
						break;
					}
				}
			}

			$download->{'composer.json'} = $this->query->getFile($name, 'composer.json');
			if ($download->{'composer.json'}) {
				$composer = json_decode($download->{'composer.json'});
				$download->{'composer:package_name'} = $composer->name;
				if (!$download->tags) {
					$download->tags = $composer->keywords;
				}

				if ($composer->type === 'elgg-plugin') {
					$download->{'manifest.xml'} = $this->query->getFile($name, 'manifest.xml');
				}
			}

			$download->{'package.json'} = $this->query->getFile($name, 'package.json');

			$this->query->authenticate();
		});
	}

	/**
	 * Sync releases from github
	 *
	 * @param Download $download Download object
	 *
	 * @return mixed
	 */
	public function syncReleases(Download $download) {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () use ($download) {

			$name = $download->{'github:package_name'};
			if (!$name) {
				return;
			}

			$this->query->authenticate($download->getOwnerEntity());

			$releases = $this->query->getReleases($name);
			if (!$releases) {
				return;
			}

			$releases = array_reverse($releases);

			foreach ($releases as $release) {
				$tag = $release['tag_name'];
				$body = $release['body'];
				$created_at = $release['created_at'];

				if ($download->getRelease($tag)) {
					continue;
				}

				if ($body) {
					$body = htmlspecialchars($body);
					$body = MarkdownExtra::defaultTransform($body);
				}

				$release = new Release();
				$release->owner_guid = $download->owner_guid;
				$release->container_guid = $download->guid;
				$release->description = $body;
				$release->version = $tag;

				list($username, $repository) = explode('/', $name, 2);
				$release->setFilename("{$repository}-{$tag}.zip");

				try {
					$release->open('write');
					$release->write($this->query->getZip($name, $tag));
					$release->close();

					$release->save();

					$release->time_created = strtotime($created_at);
					$release->save();

					$river = new River();
					$river->add($release);
				} catch (\Exception $ex) {
					$release->delete();
				}
			}

			$this->query->authenticate();
		});
	}

	/**
	 * Setup webhooks
	 *
	 * @param Download $download Download object
	 *
	 * @return mixed
	 */
	public function setupWebhook(Download $download) {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () use ($download) {
			$name = $download->{'github:package_name'};
			if (!$name) {
				return;
			}

			if ($download->{'github:webhook_id'}) {
				return;
			}

			if (!$download->{'github:secret'}) {
				$download->{'github:secret'} = generate_random_cleartext_password();
			}

			$this->query->authenticate($download->getOwnerEntity());

			list($username, $package) = explode('/', $name, 2);

			try {
				$result = $this->query->repo()->hooks()->create($username, $package, [
					'name' => 'web',
					'active' => true,
					'events' => [
						'release',
					],
					'config' => [
						'url' => elgg_normalize_url(elgg_generate_url('github:webhook', [
							'guid' => $download->guid,
						])),
						'content_type' => 'json',
						'secret' => $download->{'github:secret'},
					],
				]);

				$download->{'github:webhook_id'} = $result->id;
			} catch (\Exception $ex) {
				elgg_log($ex, 'error');
			}

			$this->query->authenticate();
		});
	}
}