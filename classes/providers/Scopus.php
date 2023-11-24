<?php

/**
 * @file classes/providers/Scopus.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Scopus
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to retrieve the citation count using the Scopus service
 */

namespace APP\plugins\generic\mostCited\classes\providers;

use APP\plugins\generic\mostCited\classes\clients\HttpClient;
use APP\plugins\generic\mostCited\classes\Settings;
use Exception;
use InvalidArgumentException;

class Scopus implements ProviderInterface
{
    private const API_URL = 'https://api.elsevier.com/content/search/scopus?query=DOI%s&apiKey=%s&field=eid,citedby-count';

    /**
     * @copydoc ProviderInterface::getCitationCount()
     */
    public function getCitationCount(string $doi, Settings $settings): int
    {
        $apiKey = $settings->scopusKey ?? null;
        if (empty($doi) || empty($apiKey)) {
            throw new InvalidArgumentException('Credentials or DOI missing');
        }
        $url = sprintf(static::API_URL, urlencode('("' . $doi . '")'), $apiKey);
        $data = json_decode(HttpClient::get($url, 'application/json'), true);

        $count = $data['search-results']['entry'][0]['citedby-count']
            ?? throw new Exception('JSON path "search-results.entry[0].citedby-count" not found, perhaps the API has changed');

        return (int) $count;
    }
}
