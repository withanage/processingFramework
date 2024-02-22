<?php

import('classes.handler.Handler');
class pdfValidatorHandler extends Handler {

	public $submission;
	public $publication;
	protected $_plugin;

	function __construct() {

		parent::__construct();

		$this->_plugin = PluginRegistry::getPlugin('generic', PDF_VALIDATOR_PLUGIN_NAME);
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_REVIEWER, ROLE_ID_AUTHOR),
			array('validatePDF')
		);
	}

	function initialize($request) {

		parent::initialize($request);
		$this->submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
		$this->publication = $this->submission->getLatestPublication();
		$this->setupTemplate($request);
	}

	public function validatePDF($args, $request)
	{
		import('plugins.generic.pdfValidator.controllers.grid.form.PDFValidatorForm');


	}

}
