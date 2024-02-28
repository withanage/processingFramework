<?php

namespace services;
use classes\Validator;


import('plugins.generic.validationFramework.classes.Validator');

class JHOVEValidator extends Validator
{

	public function __construct($plugin)
	{
		parent::__construct($plugin);

	}


	public function run($validatableObject)  : void
	{
		$output=null;
		$retval=null;

		try {
			$command = $this->getToolPathName().'  -kr -h xml -m pdf-hul '.$validatableObject;
			exec($command, $output, $retval);
			$outputString = implode('', $output);
			$this->output = $this->	formatResults($outputString);
		}
		catch (\Exception $e) {
			$this->output = $e->getMessage();
		}


	}




	public function getToolName(): string
	{
		return 'jhove';
	}

	public function getToolExecutable(): string
	{
		return 'jhove';
	}

	public function formatResults(string $result): string
	{
		return $result;
	}
}
