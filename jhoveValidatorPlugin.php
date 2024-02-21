<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.jhoveValidator.classes.services.ServiceJHOVE');

class jhoveValidatorPlugin extends GenericPlugin
{
	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path);

		if ($success && $this->getEnabled()) {

			HookRegistry::register('Publication::validatePublish', [$this, 'validate']);
		}

		return $success;
	}

	public function getDescription()
	{
		return __('plugins.generic.jhoveValidator.description');
	}

	public function getActions($request, $actionArgs)
	{

		// Get the existing actions
		$actions = parent::getActions($request, $actionArgs);

		// Only add the settings action when the plugin is enabled
		if (!$this->getEnabled()) {
			return $actions;
		}


		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$linkAction = new LinkAction(
			'settings',
			new AjaxModal(
				$router->url($request, null, null, 'manage', null, ['verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic']),
				$this->getDisplayName()
			),
			__('manager.plugins.settings'),
			null
		);

		array_unshift($actions, $linkAction);

		return $actions;
	}

	public function getDisplayName()
	{
		return __('plugins.generic.jhoveValidator.displayName');
	}

	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':

				$this->import('jhoveValidatorPluginSettingsForm');
				$form = new jhoveValidatorPluginSettingsForm($this);

				if (!$request->getUserVar('save')) {
					$form->initData();
					return new JSONMessage(true, $form->fetch($request));
				}

				$form->readInputData();
				if ($form->validate()) {
					$form->execute();
					return new JSONMessage(true);
				}
		}
		return parent::manage($args, $request);
	}

	public function validate($hookName, $args)
	{
		$errors =& $args[0];
		$publication = $args[1];
		$submission = $args[2];
		$request = PKPApplication::get()->getRequest();
		$context = $request->getContext();
		$includedServices = '';

		if (Config::getVar('jhoveValidator', 'enableJhove') === 1 || $this->getSetting($context->getId(), 'enableJhove') == 1) {
			$errors = $errors + (new ServiceDOAJ())->validate($publication, $submission, $context)->getErrors();
			$includedServices = $includedServices . ' DOAJ,';
		}
		$includedServices = rtrim($includedServices, ',');
		if (!empty($errors)) {
			$errors[] = __(
				'plugins.generic.jhoveValidator.publication.services',
				array('services' => $includedServices));
		}

	}

	function getSetting($contextId, $name)
	{
		switch ($name) {
			case 'enableJhove':
				$config_value = Config::getVar('jhoveValidator', 'openair');
				break;
			default:
				return parent::getSetting($contextId, $name);
		}

		return $config_value ?: parent::getSetting($contextId, $name);
	}
}
