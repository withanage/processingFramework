<?php

import('lib.pkp.classes.form.Form');

class ProcessingFrameworkForm extends Form
{
	function __construct($request, $plugin, $publication, $submission)
	{
		$this->_submission = $submission;
		$this->_publication = $publication;

		parent::__construct($plugin->getTemplateResource('ProcessingFramework.tpl'));
		AppLocale::requireComponents(LOCALE_COMPONENT_APP_EDITOR, LOCALE_COMPONENT_PKP_SUBMISSION);

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));



	}
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		return parent::fetch($request, $template, $display);
	}


}
