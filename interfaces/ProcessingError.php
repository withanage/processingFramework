<?php
namespace  ProcessingFramework;
interface ProcessingError{
	public function getErrorName():string;

	public function getErrorCode():int;

	public function getMessage():string;

	public function getTackTrace(): string;

}
