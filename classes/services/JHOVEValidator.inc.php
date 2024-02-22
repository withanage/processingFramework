<?php

namespace services;
use pdfValidator;
use Publication;
use Submission;

import('plugins.generic.pdfValidator.classes.pdfValidator');

class JHOVEValidator extends pdfValidator
{

	public function validate(Publication $publication, Submission $submission, $context)
	{
		$pdfValidationResults = $this->validatePDF($publication);

		$this->errors = array_merge(
			$pdfValidationResults,

		);
		return $this;
	}

	function validatePDF(Publication $publication): bool
	{
		$validationStatus = true;

		return $validationStatus;
	}
}
