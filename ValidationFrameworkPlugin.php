<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.validationFramework.classes.services.JHOVEValidator');

class ValidationFrameworkPlugin extends GenericPlugin
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
		return __('plugins.generic.validationFramework.description');
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
		return __('plugins.generic.validationFramework.displayName');
	}

	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':

				$this->import('ValidationFrameworkPluginSettingsForm');
				$form = new ValidationFrameworkPluginSettingsForm($this);

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




	function getSetting($contextId, $name)
	{
		switch ($name) {
			case 'enableJhove':
				$config_value = Config::getVar('validationFramework', 'jhove');
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

				$submissionId = $submissionFile->getData('submissionId');
				$submission = Services::get('submission')->get($submissionId);
				$stageId = (int) $request->getUserVar('stageId');
				$submissionStageId = $submission->getData('stageId');
				$roles = $request->getUser()->getRoles($request->getContext()->getId());

				$accessAllowed = false;
				foreach ($roles as $role) {
					if (in_array($role->getId(), [ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT])) {
						$accessAllowed = true;
						break;
					}
				}
				if (in_array(strtolower($fileExtension), static::getSupportedMimetypes()) && $accessAllowed &&
					in_array($stageId, $this->getAllowedWorkflowStages()) && in_array($submissionStageId, $this->getAllowedWorkflowStages())) {

					$this->validationFrameworkAction($row, $dispatcher, $request, $submissionFile);
				}
			}
		}
	}
	private function validationFrameworkAction($row, Dispatcher $dispatcher, PKPRequest $request, $submissionFile): void {

		$submissionId = $submissionFile->getData('submissionId');
		$stageId = (int) $request->getUserVar('stageId');

		$path = $dispatcher->url($request, ROUTE_PAGE, null, 'validationFramework', 'validateFile', null,
			array(
				'submissionId' => $submissionId,
				'fileId' => $submissionFile->getData('fileId'),
				'stageId' => $stageId
			));
		$pathRedirect = $dispatcher->url($request, ROUTE_PAGE, null, 'workflow', 'access',
			array(
				'submissionId' => $submissionId,
				'fileId' => $submissionFile->getData('fileId'),
				'stageId' => $stageId
			));

		import('lib.pkp.classes.linkAction.request.AjaxAction');
		$linkAction = new LinkAction(
			'parse',
			new PostAndRedirectAction($path, $pathRedirect),
			__('plugins.generic.validationFramework.links.fileValidate')
		);
		$row->addAction($linkAction);



	}



	public function callbackLoadHandler($hookName, $args) {

		$page = $args[0];
		$op = $args[1];

		switch ("$page/$op") {
			case 'validationFramework/validateFile':
				define('HANDLER_CLASS', 'validationFrameworkHandler');
				define('FILE_VALIDATOR_PLUGIN_NAME', $this->getName());
				$args[2] = $this->getPluginPath() .DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR. 'validationFrameworkHandler.inc.php';
				break;
		}

		return false;
	}


	public function getSupportedMimeTypes():  array
	{
		return  array('application/pdf');
	}
	public function getAllowedWorkflowStages() {
		return [
			WORKFLOW_STAGE_ID_EDITING,
			WORKFLOW_STAGE_ID_SUBMISSION,
			WORKFLOW_STAGE_ID_PRODUCTION
		];
	}

}
