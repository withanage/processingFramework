<?php


abstract class Validator
{

	protected array $errors = [];
	protected string $pluginToolsPath;
	protected string $output;
	protected \Plugin $plugin;
	public function __construct( \Plugin $plugin)
	{
		$this->pluginToolsPath = \Core::getBaseDir().DIRECTORY_SEPARATOR.$plugin->getPluginPath().DIRECTORY_SEPARATOR.'bin';

	}
	abstract public function validate(string $validatableObject) : void;

	public function getResult(): string{
		$this->formatResults($this->output);
	return  $this->output;
	}
	abstract public function formatResults(string $result) :string;

	abstract public function getServiceName(): string;
	abstract public function getServicePath(): string;

	public function getLocalServicePath() :string
	{
	return $this->pluginToolsPath.DIRECTORY_SEPARATOR.$this->getServiceName().DIRECTORY_SEPARATOR.$this->getServicePath();
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

}
