<?php

import ('plugins.generic.processingFramework.classes.Service');
import ('plugins.generic.processingFramework.classes.FormattedResults');
import('lib.pkp.classes.file.PrivateFileManager');
abstract class ServiceValidator extends Service
 {

	protected array $errors = [];
	protected string $pluginToolsPath;
	protected string $output = '';
	protected FormattedResults $formattedResults;

	public function getOutput(): string
	{
		return $this->output;
	}
	protected \Plugin $plugin;
	public function __construct( \Plugin $plugin, string $filePath)
	{
		$this->pluginToolsPath = \Core::getBaseDir().DIRECTORY_SEPARATOR.$plugin->getPluginPath().DIRECTORY_SEPARATOR.'bin';
		$fileManager = new PrivateFileManager();
		$fullPath = $fileManager->getBasePath() . DIRECTORY_SEPARATOR .$filePath;
		if(in_array(mime_content_type($fullPath),$this->getSupportedMimeTypes())){
			$this->executeCommand($fullPath);
			$this->validate();
		}

	}



	public function getErrors(): array
	{
		return $this->errors;
	}


}
