<?php

namespace services;
use classes\Validator;

import('plugins.generic.fileValidator.classes.Validator');

class JHOVEValidator extends Validator
{

	public function __construct($plugin)
	{
		parent::__construct($plugin);

	}


	public function run($filePath)  : void
	{
		$output=null;
		$retval=null;

		try {
			$command = $this->getToolPathName().'  -kr  PDF-hul '.$filePath;
			exec($command, $output, $retval);
			$this->output = $output;
		}
		catch (\Exception $e) {
			print($e->getMessage());
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
}
