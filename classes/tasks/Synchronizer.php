<?php

/**
 * @file classes/tasks/Synchronizer.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 *
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class Synchronizer
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class for cron job functions.
 */

namespace APP\plugins\generic\mostCited\classes\tasks;

use APP\core\Application;
use APP\facades\Repo;
use APP\plugins\generic\mostCited\classes\jobs\GenerateCache;
use APP\plugins\generic\mostCited\classes\jobs\SynchronizeCitations;
use APP\plugins\generic\mostCited\MostCitedPlugin;
use APP\submission\Submission;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use PKP\context\Context;
use PKP\scheduledTask\ScheduledTask;

class Synchronizer extends ScheduledTask
{
    private const MAXIMUM_SUBMISSIONS_PER_JOB = 10;

    /**
     * @copydoc ScheduledTask::getName()
     */
    public function getName(): string
    {
        return __('plugins.generic.mostCited.scheduledTask');
    }

    /**
     * This function is called via cron job or acron plugin.
     *
     * @copydoc ScheduledTask::executeActions()
     */
    public function executeActions(): bool
    {
        $plugin = MostCitedPlugin::getInstance();
        collect(Application::getContextDAO()->getAll()->toIterator())
            ->map(fn (Context $context) => $context->getId())
            ->filter(fn (int $contextId) => $plugin->getEnabled($contextId))
            ->each($this->synchronizeCitations(...));

        return true;
    }

    /**
     * Creates a set of jobs to retrieve citation metrics, and once complete, generates a cached feed.
     */
    public function synchronizeCitations(int $contextId): void
    {
        $synchronizationJobs = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByHasDois(true)
            ->filterByStatus([Submission::STATUS_PUBLISHED])
            ->getIds()
            ->chunk(static::MAXIMUM_SUBMISSIONS_PER_JOB)
            ->map(fn (Collection $submissionIds) => new SynchronizeCitations($contextId, $submissionIds->toArray()))
            ->toArray();

        Bus::batch($synchronizationJobs)
            ->then(fn (Batch $batch) => dispatch(new GenerateCache($contextId)))
            ->dispatch();
    }
}
