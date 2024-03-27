<?php

namespace jhove;

import('plugins.generic.processingFramework.classes.FormattedResults');

use DOMDocument;
use FormattedResults;

class JHOVEFormattedResults extends  FormattedResults
{
	protected string $input;
	protected array $results;

	public function __construct(string $input)

	{
		parent::__construct($input);
	}


	function createRows(): void
	{

		$xml = new DOMDocument();
		$xml->loadXML($this->input);
		$markdown = '';

		foreach ($xml->documentElement->childNodes as $node) {
			$markdown .= $this->xmlNodeToString($node);
		}

	   $resultRow  = new \FormattedRow('INFO',$markdown,time());
	  	$this->addRow($resultRow);


	}



	function xmlNodeToString($node, $level = 0) :string {
		$markdown = '';

		// Handle different types of nodes
		switch ($node->nodeType) {
			case XML_ELEMENT_NODE:
				$tagName = $node->tagName;
				switch ($tagName) {
					case 'jhove':
						foreach ($node->childNodes as $child) {
							$markdown .= $this->xmlNodeToString($child, $level);
						}
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

				// Process child nodes recursively
				foreach ($node->childNodes as $child) {
					$markdown .= $this->xmlNodeToString($child, $level + 1);
				}
				break;
			case XML_TEXT_NODE:
				// Ignore text nodes
				break;
			default:
				// Handle other types of nodes here if needed
				break;
		}

		return $markdown;
	}
}
