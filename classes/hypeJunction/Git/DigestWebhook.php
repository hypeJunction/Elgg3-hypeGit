<?php

namespace hypeJunction\Git;

use Elgg\BadRequestException;
use Elgg\EntityNotFoundException;
use Elgg\HttpException;
use Elgg\Request;
use hypeJunction\Downloads\Download;

class DigestWebhook {

	public function __invoke(Request $request) {
		return elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () use ($request) {
			elgg_set_viewtype('json');

			elgg_set_http_header('Content-Type: application/json');

			$payload = _elgg_services()->request->getContent();

			$entity = $request->getEntityParam();
			if (!$entity instanceof Download) {
				throw new EntityNotFoundException();
			}

			try {
				if (empty($payload)) {
					throw new BadRequestException('Payload is empty');
				}

				$sig_header = _elgg_services()->request->server->get("X-Hub-Signature");
				$event = _elgg_services()->request->server->get("X-Github-Event");

				list($algo, $hash) = explode('=', $sig_header, 2);

				$expected_hash = hash_hmac($algo, $payload, $entity->{'github:secret'});

				if (!hash_equals($expected_hash, $hash)) {
					throw new BadRequestException("Invalid signature");
				}

				$data = json_decode($payload, true);

				$result = elgg_trigger_plugin_hook($event, 'github', [
					'payload' => $data,
					'entity' => $entity,
				]);

				if ($result === false) {
					throw new HttpException('Event was not digested because one of the handlers refused to process data', ELGG_HTTP_INTERNAL_SERVER_ERROR);
				}
			} catch (HttpException $exception) {
				return elgg_ok_response([
					'error' => $exception->getMessage()
				], '', null, $exception->getCode());
			}

			return elgg_ok_response(['result' => $result]);
		});
	}
}