<?php
import('lib.pkp.classes.form.Form');
class ProcessingFrameworkPluginSettingsForm extends Form
{
	public $plugin;

	/**
	 * @copydoc Form::__construct()
	 */
	public function __construct($plugin) {

		// Define the settings template and store a copy of the plugin object
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
		$this->plugin = $plugin;

		// Always add POST and CSRF validation to secure your form.
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Load settings already saved in the database
	 *
	 * Settings are stored by context, so that each journal or press
	 * can have different settings.
	 */
	public function initData() {
		$contextId = Application::get()->getRequest()->getContext()->getId();
		$this->setData('enableJhove', $this->plugin->getSetting($contextId, 'enableJhove'));

		parent::initData();
	}

	/**
	 * Load data that was submitted with the form
	 */
	public function readInputData() {
		$this->readUserVars(['enableJhove']);
		parent::readInputData();
	}


	public function fetch($request, $template = null, $display = false) {
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_DEFAULT,
			LOCALE_COMPONENT_APP_COMMON,
			LOCALE_COMPONENT_PKP_DEFAULT,
			LOCALE_COMPONENT_PKP_COMMON,
			LOCALE_COMPONENT_PKP_USER
		);


		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->plugin->getName());


		return parent::fetch($request, $template, $display);
	}


	public function execute(...$functionArgs) {
		$contextId = Application::get()->getRequest()->getContext()->getId();
		$this->plugin->updateSetting($contextId, 'enableJhove', $this->getData('enableJhove'));

		import('classes.notification.NotificationManager');
		$notificationMgr = new NotificationManager();
		$notificationMgr->createTrivialNotification(
			Application::get()->getRequest()->getUser()->getId(),
			NOTIFICATION_TYPE_SUCCESS,
			['contents' => __('common.changesSaved')]
		);

		return parent::execute(...$functionArgs);
	}
}
