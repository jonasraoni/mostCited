<?php

/**
 * @file classes/jobs/SynchronizeCitations.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SynchronizeCitations
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to harvest/synchronize the number of citations
 */

namespace APP\plugins\generic\mostCited\classes\jobs;

use APP\facades\Repo;
use APP\plugins\generic\mostCited\classes\providers\ProviderInterface;
use APP\plugins\generic\mostCited\MostCitedPlugin;
use Illuminate\Bus\Batchable;
use InvalidArgumentException;
use PKP\jobs\BaseJob;

class SynchronizeCitations extends BaseJob
{
    use Batchable;

    /**
     * Constructor
     * @param int[] $submissionIds
     */
    public function __construct(protected int $contextId, protected array $submissionIds)
    {
        parent::__construct();
    }

    /**
     * Downloads and stores the citation counts for each submission
     */
    public function handle(): void
    {
        $settings = MostCitedPlugin::getSettings($this->contextId);
        foreach ($this->submissionIds as $submissionId) {
            $submission = Repo::submission()->get($submissionId);
            if (!$submission) {
                continue;
            }
            $provider = $this->getProvider($settings->provider);
            $count = $provider->getCitationCount($submission->getCurrentPublication()->getDoi(), $settings);
            Repo::submission()->edit($submission, [MostCitedPlugin::CITATION_COUNT_FIELD => $count]);
        }
    }

    /**
     * Retrieves a provider instance given its non-namespaced class name
     */
    private function getProvider(string $provider): ProviderInterface
    {
        foreach (MostCitedPlugin::getInstance()->getProviders() as $fullClassName) {
            $className = strtolower(basename(str_replace('\\', '/', $fullClassName)));
            if ($className === $provider) {
                return new $fullClassName();
            }
        }
        throw new InvalidArgumentException("Unknown provider \"{$provider}\"");
    }
}
