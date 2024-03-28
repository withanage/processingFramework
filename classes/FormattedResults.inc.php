<?php

import ('plugins.generic.processingFramework.classes.FormattedRow');
abstract class FormattedResults
{
	protected string $input;
	private $rows = [];

	protected array $errors = [];

	public function getErrors(): array
	{
		return $this->errors;
	}
	public function __construct(string $input, array $errors)

	{
		$this->input = $input;
		$this->errors = $errors;
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
		$markdown = '|';
		$rows = $this->getRows();
		foreach ($rows as $formattedRow) {
			$markdown.= $formattedRow->getTime().'|';
			$markdown.= $formattedRow->getInfo().'|';
			$markdown.= $formattedRow->getMessage().'|';
		}
		return  $markdown;

	}


}
