<?php
import('plugins.generic.jhoveValidator.classes.jhoveValidator');

class ServiceJHOVE extends jhoveValidator
{

	public function validate(Publication $publication, Submission $submission, $context)
	{
		$pdfValidationResults = $this->validatePDF($publication);

		$this->errors = array_merge(
			$pdfValidationResults,

		);
		return $this;
	}
}
