<?php

/**
 * @file classes/MostCitedSubmissions.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MostCitedSubmissions
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to generate and store a list of the most cited submissions
 */

namespace APP\plugins\generic\mostCited\classes;

use APP\facades\Repo;
use APP\plugins\generic\mostCited\MostCitedPlugin;
use APP\submission\Submission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MostCitedSubmissions
{
    /**
     * Create a new job instance
     */
    public function __construct(protected int $contextId)
    {
    }

    /**
     * This function retrieves the list of most cited submissions and caches the result permanently.
     */
    public function get(bool $invalidate = false): array
    {
        $settings = MostCitedPlugin::getSettings($this->contextId);
        $generator = fn () => $this->getSubmissions($settings->quantity);
        $cacheKey = implode('.', [MostCitedPlugin::class, 'submissions', $this->contextId, $settings->provider, $settings->quantity]);
        if ($invalidate) {
            $submissions = $generator();
            Cache::put($cacheKey, $submissions);
            return $submissions;
        }
        return Cache::rememberForever($cacheKey, $generator);
    }

    /**
     * Retrieve the most cited submissions
     */
    private function getSubmissions(int $limit): array
    {
        return Repo::submission()->getCollector()
            ->filterByContextIds([$this->contextId])
            ->filterByHasDois(true)
            ->filterByStatus([Submission::STATUS_PUBLISHED])
            ->limit($limit)
            ->getQueryBuilder()
            ->reorder()
            ->join('submission_settings AS mostCited', 'mostCited.submission_id', '=', 's.submission_id')
            ->where('mostCited.setting_name', '=', MostCitedPlugin::CITATION_COUNT_FIELD)
            ->orderByDesc('mostCited.setting_value')
            ->select('s.submission_id', DB::raw('mostCited.setting_value AS citations'))
            ->get()
            ->mapWithKeys(function (object $rawSubmission) {
                $submissionId = $rawSubmission->submission_id;
                $submission = Repo::submission()->get($submissionId);
                $publication = $submission->getCurrentPublication();
                return [
                    $submissionId => [
                        'submissionId' => $submissionId,
                        'submissionTitle' => $publication->getLocalizedTitle(),
                        'submissionSubtitle' => $publication->getLocalizedData('subtitle', $submission->getData('locale')),
                        'submissionAuthor' => $publication->getShortAuthorString(),
                        'citations' => (int) $rawSubmission->citations
                    ]
                ];
            })
            ->toArray();
    }
}
