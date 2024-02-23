<?php

namespace classes;
abstract class Validator
{

	protected array $errors = [];
	protected string $pluginToolsPath;

	protected array $output;

	protected \Plugin $plugin;
	public function __construct( \Plugin $plugin)
	{

		$this->pluginToolsPath = \Core::getBaseDir().DIRECTORY_SEPARATOR.$plugin->getPluginPath().DIRECTORY_SEPARATOR.'bin';


	}
	abstract public function run(string $filePath) : void;

	public function getResult(): array{
	return  $this->output;
	}
	abstract public function getToolName(): string;
	abstract public function getToolExecutable(): string;

	public function getToolPathName()
	{
	return $this->pluginToolsPath.DIRECTORY_SEPARATOR.$this->getToolName().DIRECTORY_SEPARATOR.$this->getToolExecutable();
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

}
