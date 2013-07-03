<?php
/**
* 
*/
class Story
{
	public $title;
	public $author;
	public $category;
	public $URL;
	function __construct($title ="", $author="", $category="", $URL ="")
	{
		$this->title = $title;
		$this->author = $author;
		$this->category = $category;
	}
}
?>