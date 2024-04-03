<?php

namespace jhove;

import('plugins.generic.processingFramework.classes.FormattedResults');
import('plugins.generic.processingFramework.classes.jhove.xmlUtils');

use DOMDocument;
use FormattedResults;


class JHOVEFormattedResults extends  FormattedResults
{


	protected array $results;

	public function __construct(string $input, array $errors)

	{
		parent::__construct($input, $errors);
	}

	function createRow(): void
	{
		$markdown = '';

		if ($this->errors) {
			foreach ($this->errors as $error){
				$resultRow = new \FormattedRow(PF_ERROR, $error);
				$this->addRow($resultRow);
			}
		}
		else {
			$xml = new DOMDocument();
			$xml->loadXML($this->input);

			foreach ($xml->documentElement->childNodes as $node) {
				$markdown .= xmlUtils::xmlNodeToString($node);
			}
			$resultRow = new \FormattedRow(PF_INFO, $markdown);
			$this->addRow($resultRow);


		}


	}

}
