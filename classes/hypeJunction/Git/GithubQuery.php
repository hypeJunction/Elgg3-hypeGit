<?php

namespace hypeJunction\Git;

use Elgg\Di\ServiceFacade;
use Github\Api\Repo;
use Github\Client;

class GithubQuery {

	use ServiceFacade;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructor
	 *
	 * @param Client $client Client
	 */
	public function __construct(Client $client) {
		$this->client = $client;
		$this->authenticate();
	}

	public function name() {
		return 'github.query';
	}

	/**
	 * Authenticate
	 * @param \ElggUser|null $user User
	 * @return void
	 */
	public function authenticate(\ElggUser $user = null) {
		$user_guid = $user ? $user->guid : null;
		$token = elgg_get_plugin_user_setting('github-token', $user_guid, 'hypeGit');
		if ($token) {
			$this->client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);
		}
	}

	/**
	 * Instantiate repo endpoint
	 * @return Repo
	 */
	public function repo() {
		return $this->client->api('repo');
	}

	/**
	 * Get package information
	 *
	 * @param string $name Package name, e.g. hypeJunction/Elgg3-hypeMapsOpen
	 *
	 * @return array|null
	 */
	public function getPackage($name) {
		try {
			list($username, $package) = explode('/', $name, 2);

			return $this->repo()->show($username, $package);
		} catch (\Exception $ex) {
			return null;
		}
	}

	/**
	 * Get release information
	 *
	 * @param string $name Package name, e.g. hypeJunction/Elgg3-hypeMapsOpen
	 *
	 * @return array|null
	 */
	public function getReleases($name) {
		try {
			list($username, $package) = explode('/', $name, 2);

			return $this->repo()->releases()->all($username, $package);
		} catch (\Exception $ex) {
			return null;
		}
	}

	/**
	 * Get contents of the zipball
	 *
	 * @param string $name      Package name
	 * @param string $reference Tag or commit reference
	 *
	 * @return null|string
	 */
	public function getZip($name, $reference) {
		try {
			list($username, $package) = explode('/', $name, 2);

			$release = $this->repo()->contents()->archive($username, $package, 'zipball', $reference);

			return $release;
		} catch (\Exception $ex) {
			return null;
		}
	}

	/**
	 * Get contents of a file
	 *
	 * @param string $name      Package name
	 * @param string $reference Tag or commit reference
	 *
	 * @return null|string
	 */
	public function getFile($name, $path) {
		try {
			list($username, $package) = explode('/', $name, 2);

			return $this->repo()->contents()->download($username, $package, $path);
		} catch (\Exception $ex) {
			return null;
		}

	}
}