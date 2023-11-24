<?php

/**
 * @file classes/jobs/GenerateCache.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class GenerateCache
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to regenerate the most cited submissions cache
 */

namespace APP\plugins\generic\mostCited\classes\jobs;

use APP\plugins\generic\mostCited\classes\MostCitedSubmissions;
use Illuminate\Bus\Batchable;
use PKP\jobs\BaseJob;

class GenerateCache extends BaseJob
{
    use Batchable;

    /**
     * Constructor
     * @param int $contextId
     */
    public function __construct(protected int $contextId)
    {
        parent::__construct();
    }

    /**
     * Retrieves the most cited submissions and caches them
     */
    public function handle(): void
    {
        (new MostCitedSubmissions($this->contextId))->get(true);
    }
}
