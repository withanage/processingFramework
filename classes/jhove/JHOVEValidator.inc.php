<?php


namespace jhove;
use Validator;

import('plugins.generic.processingFramework.classes.Validator');
import('plugins.generic.processingFramework.classes.jhove.JHOVEFormattedResults');
import ('plugins.generic.processingFramework.classes.FormattedResults');

class JHOVEValidator extends Validator

{


	public function __construct($plugin, $filePath)
	{
		parent::__construct($plugin, $filePath);

	}
	public function getSupportedMimeTypes():  array
	{
		return  array('application/pdf');
	}

	public function executeCommand($validatableObject): bool
	{
		$output = null;
		$retval = null;

			$command = $this->getLocalServicePath() . '  -kr -h xml -m pdf-hul ' . $validatableObject;
			exec($command, $output, $retval);
			$this->output = implode(' ', $output);
		if ($retval == 0) {
			return true;
		} else {
			$this->output = PF_ERROR.''.$command;
			$this->errors[] = __('plugins.generic.processingFramework.notification.jhove_application_error');
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





}
