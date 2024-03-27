<?php

namespace jhove;

import('plugins.generic.processingFramework.classes.FormattedResults');

use FormattedResults;

class JHOVEFormattedResults extends  FormattedResults
{
	protected string $input;
	protected array $results;

	public function __construct(string $input)

	{
		$this->input = $input;

	}



	function getResults(): array
	{
		$x= $this->input;
	}
}
