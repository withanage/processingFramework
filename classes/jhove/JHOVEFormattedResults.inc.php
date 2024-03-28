<?php

namespace jhove;

import('plugins.generic.processingFramework.classes.FormattedResults');

use DOMDocument;
use FormattedResults;

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
				$markdown .= $this->xmlNodeToString($node);
			}

		}
		$resultRow = new \FormattedRow(PF_INFO, $markdown);
		$this->addRow($resultRow);


	}

	function xmlNodeToString($node, $level = 0) :string {
		$markdown = '';


		switch ($node->nodeType) {
			case XML_ELEMENT_NODE:
				$tagName = $node->tagName;
				switch ($tagName) {
					case 'jhove':
						foreach ($node->childNodes as $child) {
							$markdown .= $this->xmlNodeToString($child, $level);
						}
						break;
					case 'reportingModule':
						$markdown .= "Module: " . $node->nodeValue . "  ";
						break;
					case 'version':
						$markdown .= "PDF version: " . $node->nodeValue . "  ";
						break;
					case 'status':
						$markdown .= "Status: " . $node->nodeValue . "  ";
						break;
					default:
						break;
				}

				foreach ($node->childNodes as $child) {
					$markdown .= $this->xmlNodeToString($child, $level + 1);
				}
				break;
			case XML_TEXT_NODE:
				break;
			default:
				break;
		}

		return $markdown;
	}
}
