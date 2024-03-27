<?php


namespace jhove;
use Validator;

import('plugins.generic.processingFramework.classes.Validator');
import('plugins.generic.processingFramework.classes.jhove.JHOVEFormattedResults');
import ('plugins.generic.processingFramework.classes.FormattedResults');

class JHOVEValidator extends Validator

{

	public function __construct($plugin)
	{
		parent::__construct($plugin);

	}

	public function executeCommand($validatableObject): bool
	{
		$output = null;
		$retval = null;

		try {
			$command = $this->getLocalServicePath() . '  -kr -h xml -m pdf-hul ' . $validatableObject;
			exec($command, $output, $retval);
			$this->output = implode(' ', $output);
			return true;

		} catch (Exception $e) {
			$this->output = $e->getMessage();
			return false;
		}

	}


	public function getServiceName(): string
	{
		return 'jhove';
	}

	public function getServicePath(): string
	{
		return 'jhove';
	}

	public function getFormattedResults($input): \FormattedResults
	{
		$formattedResults = new  JHOVEFormattedResults($input);
		return $formattedResults;
	}




}
