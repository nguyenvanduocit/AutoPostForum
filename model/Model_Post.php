<?php
class Post
	{
		public $subject; //string
		public $message; //string
		public $taglist; //array
		public $sourceSite; //string

		function __construct($subject ="", $message ="", $taglist="", $sourceSite="")
		{
			$this->subject = $subject;
			$this->message = $message;
			$this->taglist = $taglist;
			$this->sourceSite = $sourceSite;
		}
	}
?>