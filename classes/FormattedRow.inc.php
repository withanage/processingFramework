<?php

 class FormattedRow

{


	 private $info;
	 private $message;
	 private $time;

	 public function __construct(string $info, string $message, \traits\DateTime $time) {

		 $this->info = $info;

		 $this->message = $message;

		 $this->time = $time;

	 }




}
