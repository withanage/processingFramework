<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.pdfValidator.classes.services.ServiceJHOVE');

class pdfValidatorPlugin extends GenericPlugin
{
	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path);

		if ($success && $this->getEnabled()) {

			HookRegistry::register('Publication::validatePublish', [$this, 'validate']);
			HookRegistry::register('LoadHandler', array($this, 'callbackLoadHandler'));
			HookRegistry::register('TemplateManager::fetch', array($this, 'templateFetchCallback'));
			$this->_registerTemplateResource();
		}

		return $success;
	}

	public function getDescription()
	{
		return __('plugins.generic.pdfValidator.description');
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
		return __('plugins.generic.pdfValidator.displayName');
	}

	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':

				$this->import('pdfValidatorPluginSettingsForm');
				$form = new pdfValidatorPluginSettingsForm($this);

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

		if (Config::getVar('pdfValidator', 'enableJhove') === 1 || $this->getSetting($context->getId(), 'enableJhove') == 1) {
			$errors = $errors + (new ServiceJHOVE())->validate($publication, $submission, $context)->getErrors();
			$includedServices = $includedServices . ' Jhove,';
		}
		$includedServices = rtrim($includedServices, ',');
		if (!empty($errors)) {
			$errors[] = __(
				'plugins.generic.pdfValidator.publication.services',
				array('services' => $includedServices));
		}

	}

	function getSetting($contextId, $name)
	{
		switch ($name) {
			case 'enableJhove':
				$config_value = Config::getVar('pdfValidator', 'openair');
				break;
			default:
				return parent::getSetting($contextId, $name);
		}

		return $config_value ?: parent::getSetting($contextId, $name);
	}

	public function templateFetchCallback($hookName, $params)
	{

		$request = $this->getRequest();
		$router = $request->getRouter();
		$dispatcher = $router->getDispatcher();

		$templateMgr = $params[0];
		$resourceName = $params[1];
		if ($resourceName == 'controllers/grid/gridRow.tpl') {
			$row = $templateMgr->getTemplateVars('row');
			$data = $row->getData();
			if (is_array($data) && (isset($data['submissionFile']))) {
				$submissionFile = $data['submissionFile'];
				$fileExtension = strtolower($submissionFile->getData('mimetype'));
				$stageId = (int)$request->getUserVar('stageId');

				if (strtolower($fileExtension) == 'application/pdf') {
					import('lib.pkp.classes.linkAction.request.OpenWindowAction');
					$this->pdfValidatorAction($row, $dispatcher, $request, $submissionFile, $stageId);
				}
			}
		}
	}
	private function pdfValidatorAction($row, Dispatcher $dispatcher, PKPRequest $request, $submissionFile, int $stageId): void {

		$row->addAction(new LinkAction(
			'pdf_validate',
			new OpenWindowAction(
				$dispatcher->url($request, ROUTE_PAGE, null, 'pdfValidator', 'validator', null,
					array(
						'submissionId' => $submissionFile->getData('submissionId'),
						'submissionFileId' => $submissionFile->getData('id'),
						'stageId' => $stageId
					)
				)
			),
			__('plugins.generic.pdfValidator.links.pdfValidate'),
			null
		));
	}

	public function callbackLoadHandler($hookName, $args) {

		$page = $args[0];
		$op = $args[1];

		switch ("$page/$op") {
			case 'pdfValidator/validatePDF':
				define('HANDLER_CLASS', 'pdfValidatorHandler');
				define('PDF_VALIDATOR_PLUGIN_NAME', $this->getName());
				$args[2] = $this->getPluginPath() .DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR. 'pdfValidatorHandler.inc.php';
				break;
		}

		return false;
	}

}
