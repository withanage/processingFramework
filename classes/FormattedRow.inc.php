<?php

 class FormattedRow

{


	 private $info;

	 public function getInfo(): string
	 {
		 return $this->info;
	 }

	 public function setInfo(string $info): void
	 {
		 $this->info = $info;
	 }

	 public function getMessage(): string
	 {
		 return $this->message;
	 }

	 public function setMessage(string $message): void
	 {
		 $this->message = $message;
	 }

	 public function getTime()
	 {
		 return $this->time;
	 }

	 public function setTime($time): void
	 {
		 $this->time = $time;
	 }
	 private $message;
	 private $time;

	 public function __construct(string $info, string $message,  $time) {

		 $this->info = $info;

		 $this->message = $message;

		 $this->time = $time;

	 }




}
