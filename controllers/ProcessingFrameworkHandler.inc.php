<?php


use jhove\JHOVEValidator;

import('classes.handler.Handler');
import('lib.pkp.classes.file.PrivateFileManager');
import('plugins.generic.processingFramework.classes.jhove.JHOVEValidator');


class ProcessingFrameworkHandler extends Handler
{

	var $_submission = null;
	var $_publication = null;

	function __construct()
	{

		parent::__construct();
		$this->_plugin = PluginRegistry::getPlugin('generic', FILE_VALIDATOR_PLUGIN_NAME);
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_REVIEWER, ROLE_ID_AUTHOR),
			array('validateFile')
		);
	}

	function initialize($request)
	{

		parent::initialize($request);
		$this->submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
		$this->publication = $this->submission->getLatestPublication();
		$this->setupTemplate($request);
	}

	function authorize($request, &$args, $roleAssignments)
	{
		import('lib.pkp.classes.security.authorization.WorkflowStageAccessPolicy');
		$this->addPolicy(new WorkflowStageAccessPolicy($request, $args, $roleAssignments, 'submissionId', (int)$request->getUserVar('stageId')));
		return parent::authorize($request, $args, $roleAssignments);
	}

	public function PDFValidationForm($args, $request)
	{
		import('plugins.generic.processingFramework.controllers.grid.form.processingFrameworkForm');
		$ProcessingFrameworkForm = new ProcessingFrameworkForm($request, $this->_plugin, $this->publication, $this->submission);
		$ProcessingFrameworkForm->initData();
		return new JSONMessage(true, $ProcessingFrameworkForm->fetch($request));
	}
	public  function validateFile($args, $request) {
		$fileId = (int) $request->getUserVar('fileId');
		$submissionFiles = Services::get('submissionFile')->getMany([
			'fileIds' => [$fileId],
		]);
		$submissionFile = $submissionFiles->current();
		$validationResutlsSuffix = '-validation-results.txt';
		$submissionId = $submissionFile->getData('submissionId');
		$submission = Services::get('submission')->get($submissionId);
		$genreId = $submissionFile->getData('genreId');

		$fileManager = new PrivateFileManager();
		$filePath = $fileManager->getBasePath() . DIRECTORY_SEPARATOR . $submissionFile->getData('path');
		$tempFileName = tempnam(sys_get_temp_dir(), 'processingFramework');
		$jhoveValidator = new  JHOVEValidator(($this->_plugin));
		$jhoveValidator->executeCommand($filePath);
		$results = $jhoveValidator->getFormattedResults($jhoveValidator->getOutput());
		$markDownResult= $results->getMarkdownRows();

		file_put_contents($tempFileName, $markDownResult);

		$submissionDir = Services::get('submissionFile')->getSubmissionDir($submission->getData('contextId'), $submissionId);
		$newFileId = Services::get('file')->add(
			$tempFileName,
			$submissionDir . DIRECTORY_SEPARATOR . uniqid() . $validationResutlsSuffix
		);
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$newSubmissionFile = $submissionFileDao->newDataObject();
		$newName = [];
		foreach ($submissionFile->getData('name') as $localeKey => $name) {
			$newName[$localeKey] = pathinfo($name)['filename'] . $validationResutlsSuffix;
		}

		$newSubmissionFile->setAllData(
			[
				'fileId' => $newFileId,
				'assocType' => $submissionFile->getData('assocType'),
				'assocId' => $submissionFile->getData('assocId'),
				'fileStage' => $submissionFile->getData('fileStage'),
				'mimetype' => 'application.xml',
				'locale' => $submissionFile->getData('locale'),
				'genreId' => $genreId,
				'name' => $newName,
				'submissionId' => $submissionId,
			]
		);
		$newSubmissionFile = Services::get('submissionFile')->add($newSubmissionFile, $request);

		unlink($tempFileName);

		return new JSONMessage(true, array(
			'submissionId' => $submissionId,
			'fileId' => $newSubmissionFile->getData('fileId'),
			'fileStage' => $newSubmissionFile->getData('fileStage'),
		));


	}





}
