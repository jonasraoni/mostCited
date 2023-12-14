<?php

/**
 * @file classes/clients/HttpClient.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 *
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class HttpClient
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to fire HTTP requests.
 */

namespace APP\plugins\generic\mostCited\classes\clients;

use APP\core\Application;
use Exception;

class HttpClient
{
    /**
     * Get data from url
     *
     * @param string $url url to get data from
     * @param string $type Response type
     *
     * @return string Data from url
     */
    public static function get(string $url, string $type = 'text/xml'): string
    {
        $httpClient = Application::get()->getHttpClient();
        $response = $httpClient->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Accept' => $type,
                    'Content-Type' => $type,
                    'Cache-Control' => 'no-cache'
                ]
            ]
        );
        if (($status = $response->getStatusCode()) !== 200) {
            throw new Exception("Expected HTTP status 200, got {$status}");
        }
        return $response->getBody()->getContents();
    }
}
