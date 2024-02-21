<?php

abstract class jhoveValidator
{

	protected array $errors;

	abstract public function validate(Publication $publication, Submission $submission, $context);

	public function getErrors(): array
	{
		return $this->errors;
	}

}
