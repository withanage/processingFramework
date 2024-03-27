<?php

import ('plugins.generic.processingFramework.classes.FormattedRow');
abstract class FormattedResults
{
	protected string $input;
	protected array $results;

	abstract function  getResults () : array;





}
