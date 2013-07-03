<?php
	require_once '/../../model/Model_Post.php';
	require_once '/../YQL/YQL.php';

	class Crawler
	{
		
		public static $YQL;
		public static $instance = null;
		public $domain;
		function __construct()
		{
			self::$YQL = YQL::getInstance();
		}

		public static function getInstance()
		{
			if(!isset(self::$instance))
			{
				self::$instance = new Crawler();
			}
			return self::$instance;
		}

		public function getAllStoryOfCategory($categorySlug){}
		public function getAllStoryInOnePageOfCategory($contents){}
		public function getStory($storyAddress){}
	}
?>