<?php

namespace jhove;

import('plugins.generic.processingFramework.classes.FormattedResults');
import('plugins.generic.processingFramework.classes.utils.xmlUtils');

use DOMDocument;
use FormattedResults;
use xmlUtils;


class JHOVEFormattedResults extends  FormattedResults
{


	protected array $results;

	public function __construct(string $input, array $errors)

	{
		parent::__construct($input, $errors);
	}

	function createRows(): void
	{
		$markdown = '';

		if ($this->errors) {
			foreach ($this->errors as $error){
				$markdown.=  $error;
			}
		}
		else {
			$xml = new DOMDocument();
			$xml->loadXML($this->input);

			foreach ($xml->documentElement->childNodes as $node) {
				$markdown .= xmlUtils::xmlNodeToString($node);
			}

		}
		$resultRow = new \FormattedRow(PF_INFO, $markdown);
		$this->addRow($resultRow);


	}

}
