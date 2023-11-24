<?php

/**
 * @file classes/providers/ProviderInterface.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ProviderInterface
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Interface for classes which can retrieve the citation count
 */

namespace APP\plugins\generic\mostCited\classes\providers;

use APP\plugins\generic\mostCited\classes\Settings;

interface ProviderInterface
{
    /**
     * Retrieve the citation count for the given DOI
     * @param string $doi The DOI to process
     * @param Settings $settings The plugin settings
     * @return int The citation count
     */
    public function getCitationCount(string $doi, Settings $settings): int;
}
