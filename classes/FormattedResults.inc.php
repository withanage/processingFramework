<?php

import ('plugins.generic.processingFramework.classes.FormattedRow');
abstract class FormattedResults
{
	protected string $input;
	private $rows = [];
	public function __construct(string $input)

	{
		$this->input = $input;
		$this->createRows();

	}

	public function addRow(FormattedRow $item)
	{
		$this->rows[] = $item;
	}

	public function getRows(): array
	{
		return $this->rows;
	}
	abstract  function createRows(): void;

	public function getMarkdownRows(): string

	{
		$markdown = '';
		$rows = $this->getRows();
		foreach ($rows as $formattedRow) {
			$markdown.= $formattedRow->getInfo().'|';
			$markdown.= $formattedRow->getMessage().'|';
			$markdown.= $formattedRow->getTime().'|';
		}
		return  $markdown;

	}


}
