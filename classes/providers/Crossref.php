<?php

/**
 * @file classes/providers/Crossref.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Crossref
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to retrieve the citation count using the Crossref service
 */

namespace APP\plugins\generic\mostCited\classes\providers;

use APP\plugins\generic\mostCited\classes\clients\HttpClient;
use APP\plugins\generic\mostCited\classes\Settings;
use Exception;
use InvalidArgumentException;

class Crossref implements ProviderInterface
{
    private const API_URL = 'https://doi.crossref.org/servlet/getForwardLinks?usr=%s&pwd=%s&doi=%s';

    /**
     * @copydoc ProviderInterface::getCitationCount()
     */
    public function getCitationCount(string $doi, Settings $settings): int
    {
        $user = $settings->crossrefUser ?? null;
        $password = $settings->crossrefPassword ?? null;
        $role = $settings->crossrefRole ?? null;
        if (empty($user) || empty($password) || empty($doi)) {
            throw new InvalidArgumentException('Credentials or DOI missing');
        }
        if (strlen($role ?? '')) {
            $user = "{$user}/{$role}";
        }
        $url = sprintf(static::API_URL, urlencode($user), urlencode($password), urlencode($doi));
        $data = HttpClient::get($url, 'application/json');
        $xml = simplexml_load_string($data);
        $links = $xml->query_result->body->forward_link
            ?? throw new Exception('Node query_result.body.forward_link not found, perhaps the API has changed');
        return count($links ?? []);
    }
}
