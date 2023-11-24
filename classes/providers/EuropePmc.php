<?php

/**
 * @file classes/providers/EuropePmc.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EuropePmc
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to retrieve the citation count using the Europe PMC service
 */

namespace APP\plugins\generic\mostCited\classes\providers;

use APP\plugins\generic\mostCited\classes\clients\HttpClient;
use APP\plugins\generic\mostCited\classes\Settings;
use Exception;

class EuropePmc implements ProviderInterface
{
    private const API_URL = 'https://www.ebi.ac.uk/europepmc/webservices/rest/search?query=%s';

    /**
     * @copydoc ProviderInterface::getCitationCount()
     */
    public function getCitationCount(string $doi, Settings $settings): int
    {
        $url = sprintf(static::API_URL, $doi);
        $data = HttpClient::get($url, 'application/xml');
        $xml = simplexml_load_string($data);
        $count = $xml->hitCount
            ?? throw new Exception('Node hitCount not found, perhaps the API has changed');
        return (int) $count;
    }
}
