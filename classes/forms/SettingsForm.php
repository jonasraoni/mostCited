<?php

/**
 * @file classes/forms/SettingsForm.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 *
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class SettingsForm
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Class to setup the plugin settings.
 */

namespace APP\plugins\generic\mostCited\classes\forms;

use APP\core\Application;
use APP\notification\NotificationManager;
use APP\plugins\generic\mostCited\classes\jobs\GenerateCache;
use APP\plugins\generic\mostCited\classes\Settings;
use APP\plugins\generic\mostCited\classes\tasks\Synchronizer;
use APP\plugins\generic\mostCited\MostCitedPlugin;
use APP\template\TemplateManager;
use Exception;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use PKP\notification\PKPNotification;

class SettingsForm extends Form
{
    /**
     * @copydoc Form::__construct()
     */
    public function __construct(public MostCitedPlugin $plugin, public int $contextId)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * @copydoc Form::initData()
     */
    public function initData(): void
    {
        $contextId = Application::get()->getRequest()->getContext()->getId();
        MostCitedPlugin::getSettings($contextId)->toForm($this);
        parent::initData();
    }

    /**
     * @copydoc Form::readInputData()
     */
    public function readInputData(): void
    {
        $this->readUserVars(array_keys(Settings::create()->toArray()));
        parent::readInputData();
    }

    /**
     * @copydoc Form::fetch()
     *
     * @param null|mixed $template
     *
     * @throws Exception
     */
    public function fetch($request, $template = null, $display = false): ?string
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign([
            'pluginName' => $this->plugin->getName(),
            'providers' => collect($this->plugin->getProviders())
                ->map(fn (string $class) => strtolower(basename(str_replace('\\', '/', $class))))
                ->mapWithKeys(fn (string $class) => [$class => "plugins.generic.mostCited.provider.{$class}"])
                ->toArray()
        ]);

        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$args): mixed
    {
        $context = Application::get()->getRequest()->getContext();
        $contextId = $context->getId();
        $previousSettings = Settings::create()->fromJson($this->plugin->getSetting($contextId, 'settings'));
        $settings = Settings::create()->fromForm($this);
        $this->plugin->updateSetting($contextId, 'settings', $settings->toJson());

        $needsHarvesting = false;
        foreach (['crossrefPassword', 'crossrefUser', 'crossrefRole', 'provider', 'scopusKey'] as $setting) {
            if ($settings->$setting !== $previousSettings->$setting) {
                $needsHarvesting = true;
                break;
            }
        }

        if ($needsHarvesting) {
            (new Synchronizer())->synchronizeCitations($contextId);
        } else {
            dispatch(new GenerateCache($contextId));
        }

        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            PKPNotification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );
        return parent::execute(...$args);
    }
}
