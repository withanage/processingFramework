<?php

import('lib.pkp.classes.form.Form');

class PDFValidatorForm extends Form
{
	function __construct($request, $plugin, $publication, $submission)
	{
		$this->_submission = $submission;
		$this->_publication = $publication;

		parent::__construct($plugin->getTemplateResource('pdfValidator.tpl'));

		AppLocale::requireComponents(LOCALE_COMPONENT_APP_EDITOR, LOCALE_COMPONENT_PKP_SUBMISSION);

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}
}
