<?php

namespace services;
abstract class pdfValidator
{

	protected array $errors;
	protected string $inputFile;
	public function __construct($inputFile)
	{
		$this->inputFile = $inputFile;

	}
	abstract public function getValidationResults(): string;

	public function getErrors(): array
	{
		return $this->errors;
	}

}
