<?php

/**
 * @file MostCitedPlugin.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 *
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class MostCitedPlugin
 *
 * @ingroup plugins_generic_mostCited
 *
 * @brief Core plugin class, handles registration and main functionality
 */

namespace APP\plugins\generic\mostCited;

use APP\core\Application;
use APP\facades\Repo;
use APP\plugins\generic\mostCited\classes\forms\SettingsForm;
use APP\plugins\generic\mostCited\classes\MostCitedSubmissions;
use APP\plugins\generic\mostCited\classes\providers\Crossref;
use APP\plugins\generic\mostCited\classes\providers\EuropePmc;
use APP\plugins\generic\mostCited\classes\providers\ProviderInterface;
use APP\plugins\generic\mostCited\classes\providers\Scopus;
use APP\plugins\generic\mostCited\classes\Settings;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\plugins\PluginRegistry;

class MostCitedPlugin extends GenericPlugin
{
    public const CITATION_COUNT_FIELD = 'MostCitedPlugin::citationCount';

    protected static self $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        self::$instance ??= $this;
    }

    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null): bool
    {
        if (!parent::register($category, $path)) {
            return false;
        }

        if (Application::isUnderMaintenance() || !$this->getEnabled($mainContextId)) {
            return true;
        }

        $this->setupHooks();
        $this->addStylesheet();

        return true;
    }

    /**
     * Retrieve the class names of available citation providers
     *
     * @return class-string<ProviderInterface>[]
     */
    public static function getProviders(): array
    {
        return [Crossref::class, EuropePmc::class, Scopus::class];
    }

    /**
     * Retrieve the plugin settings
     */
    public static function getSettings(int $contextId): Settings
    {
        return Settings::create()->fromJson(static::getInstance()->getSetting($contextId, 'settings'));
    }

    public static function getInstance(): static
    {
        return static::$instance ??= PluginRegistry::loadPlugin('generic', 'mostCited');
    }

    /**
     * Setups the citationCount field at the submission DAO
     */
    private function setupSubmissionFields(string $hookName, array $args): bool
    {
        /** @var object $schema */
        [$schema] = $args;
        $schema->properties->{static::CITATION_COUNT_FIELD} = (object) [
            'type' => 'integer',
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        return Hook::CONTINUE;
    }

    /**
     * Setup the Acron plugin to call the plugin's scheduled task
     */
    private function setupAcronTask(string $hookName, array $args): bool
    {
        $taskFilesPath = & $args[0]; // Reference needed.
        $taskFilesPath[] = "{$this->getPluginPath()}/scheduledTasks.xml";

        return Hook::CONTINUE;
    }

    /**
     * Append the most cited content to the indexJournal.tpl
     */
    private function appendMostCitedSubmissions(string $hookName, array $args): bool
    {
        /** @var TemplateManager $smarty */
        [, $smarty, &$output] = $args;
        $request = Application::get()->getRequest();
        $contextId = $request->getContext()->getId();
        $settings = $this->getSettings($contextId);
        $submissions = (new MostCitedSubmissions($contextId))->get();
        $smarty->assign([
            'mostCitedSubmissions' => $submissions,
            'mostCitedHeadline' => $settings->header,
            'mostCitedPosition' => $settings->position
        ]);
        $output .= $smarty->fetch($this->getTemplateResource('mostCited.tpl'));
        return Hook::CONTINUE;
    }


    /**
     * Setups the hooks
     */
    private function setupHooks(): void
    {
        Hook::add('AcronPlugin::parseCronTab', $this->setupAcronTask(...));
        Hook::add('Templates::Index::journal', $this->appendMostCitedSubmissions(...));
        Hook::add('Schema::get::' . Repo::submission()->dao->schema, $this->setupSubmissionFields(...));
    }

    /**
     * Adds the plugin stylesheet
     */
    private function addStylesheet(): void
    {
        $request = Application::get()->getRequest();
        TemplateManager::getManager($request)->addStyleSheet(
            'mostCitedSubmissions',
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/css/mostCited.css"
        );
    }

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $verb): array
    {
        $actions = parent::getActions($request, $verb);
        if (!$this->getEnabled()) {
            return $actions;
        }

        $router = $request->getRouter();
        $url = $router->url($request, null, null, 'manage', null, ['verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic']);
        array_unshift($actions, new LinkAction('settings', new AjaxModal($url, $this->getDisplayName()), __('manager.plugins.settings')));
        return $actions;
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request): JSONMessage
    {
        if ($request->getUserVar('verb') !== 'settings') {
            return parent::manage($args, $request);
        }
        $contextId = $request->getContext()?->getId() ?: 0;
        $form = new SettingsForm($this, $contextId);
        if (!$request->getUserVar('save')) {
            $form->initData();
            return new JSONMessage(true, $form->fetch($request));
        }

        $form->readInputData();
        if ($form->validate()) {
            $form->execute();
            return new JSONMessage(true);
        }

        return parent::manage($args, $request);
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName(): string
    {
        return __('plugins.generic.mostCited.title');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription(): string
    {
        return __('plugins.generic.mostCited.desc');
    }
}
