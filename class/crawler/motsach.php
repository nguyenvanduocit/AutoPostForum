<?php
require_once 'crawler.php';
require_once '/../YQL/YQL.php';
require_once '/../../model/Story.php';
require_once '/../vietnamese-url.php';
class MotSach extends Crawler
{


	public $content  = "";
	public static $instance = null;
	function __construct()
	{
		parent::__construct();
		$this->domain = "http://motsach.info";
	}

	public static function getInstance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new MotSach();
		}
		return self::$instance;
	}
	/*
	*   argument
	*		$categorySlug :
	*				example : kiem_hiep
	*	return 
	*		array( <pageNumber> =>
						array(<postnumber>, 
								array(<name>, <href>)) )
	*
	*/
	public function getAllStoryOfCategory($categorySlug)
	{

//Lấy một số thông số cơ bản
		$PageObject = array();

		$categoryQuery = 'select * from html where url="'.$this->domain.'/story.php?list=story&category='.$categorySlug.'" and xpath=\'//div[@id="motsach_content_body"]\'';
		$response = self::$YQL->execute($categoryQuery);
		$result = $response->results->div;

		$pagecount = 1;
		//get page
		//$pageList = $result->div[1]->ul;
		//$PageObject["currentPage"] = filter_var($pageList->id, FILTER_SANITIZE_NUMBER_INT);
		if(isset($result->div[1]->ul) && isset($result->div[1]->ul->li))
		{
			$pageList = $result->div[1]->ul->li;
			//$PageObject["lastPage"] = count($pageList)-2; //trừ cho hai nút tới, lui ở hai đầu

//Lấy tất cả các page của category
			$pagecount = count($pageList);
			$pagecount = $pageList[$pagecount-2]->a->content;
		}
		//$PageObject['pagelist'] = $pages;
//Kết lúc lất tất cả các page
//Bắt đầu lất tất cả các link story của từng page
		//page đầu tiên có sẵn, không cần lấy lại
		$PageObject[] = $this->getAllStoryInOnePageOfCategory($result->div[2]);

		//for debug
		//return $PageObject;

		//Các page thứ 2 trở đi cần phải tải lại nội dung
		for($page = 2; $page <= $pagecount ; $page++) {
			$categoryQuery = 'select * from html where url="'.$this->domain.'/story.php?list=story&category='.$categorySlug.'&order=story_id&page='.$page.'" and xpath=\'//div[@id="motsach_content_body"]\'';
			$response = self::$YQL->execute($categoryQuery);
			$result = $response->results->div;
			$PageObject[] = $this->getAllStoryInOnePageOfCategory($result->div[2]);
		}
		//Kết thúc lấy nội dung của các page từ page thứ 2 trở đi


//Kết thúc lấy toàn bộ bài viết trong category
		return $PageObject;
	}

	public function getAllStoryInOnePageOfCategory($contents)
	{
		$contents =$contents->div;
		$result = array();
		/*
		struct
			name=>string
			href=>string
		*/
			$index = 0;
		foreach ($contents as $key => $content) {
			if(isset($content->class) && $content->class =="story_list_item")
			{
				$result[$index]["name"]=$content->div[0]->a[1]->content;
				$result[$index]["href"]=$content->div[0]->a[1]->href;
				$index++;	
			}
		}
		return $result;
	}


	////////////session for get single story
	/* Không sử dụng đệ quy
	* return story
	* hàm này là lấy tất cả các chapter cùng một lúc luôn
	*/
	public function getStory($storyAddress)
	{


		$nextChapter = null;// the link to next chapter example : story.php?story=am_cong__co_long&chapter=002


		$resultStory = new Story();
		$resultStory->URL = $storyAddress;
		//Chapter đầu tiên, nếu không có chapter thì hiển thị nội dung
		$query = 'select * from html where url="http://motsach.info/'.$storyAddress.'" and xpath=\'//form[@name="frmEditor"]\'';
		//$query = 'select * from html where url="http://motsach.info/story.php?story=16_met_vuong__vu_dinh_giang" and xpath=\'//form[@name="frmEditor"]\'';
		$response = self::$YQL->execute($query);
		$data = $response->results->form->div;
		//Get story info
		$resultStory->title = trim($data[0]->p->content);
		$resultStory->author = $data[1]->p->a->content;
		$resultStory->category = $data[2]->p->a->content;
		$resultStory->directory = "data/".sanitize_title($resultStory->category).'/'.sanitize_title($resultStory->author).'/'.sanitize_title($resultStory->title);

		if (!is_dir($resultStory->directory)) {
		  mkdir($resultStory->directory, 0, true);
		}
		else
		{
			return $resultStory->directory;
		}
		file_put_contents($resultStory->directory."/info.txt", $resultStory->title."\n".$resultStory->author."\n".$resultStory->category);

		//end get story info

		/*
		Nếu có nhiều chapter, sẽ hiện trang chapter, nếu chỉ có một chapter, sẽ hiện luôn nội, dung => cần phải kiểm tra
		*/

		foreach ($data as $key => $div) {
			if(isset($div->class) && ($div->class == "story_text"))
			{
				//Đây là truyện có một bài duy nhất
				$resultStory->mucluc = null;
				break;
			}
			else if(isset($div->id) && ($div->id=="chapter_navigator_box_top"))
			{
				if(isset($div->div->div))
				{
					//Có hai nút chuyển chapter
					if(isset($div->div->div->a))
					{
						echo "Đây là mục lục, truyện này dài kỳ<br/>";
						$nextChapter = $div->div->div->a->href;
						$resultStory->mucluc = $this->getMucLuc($data);
						$a = array_map(function($obj) { return $obj->content; },$resultStory->mucluc);
						file_put_contents($resultStory->directory."/mucluc.txt", implode("\n", $a));
					}
					else 
					{
						if(isset($div->div->div[1]->a))
						{
							echo "còn chapter sau";
							$nextChapter = $div->div->div[1]->a->href;
						}
						else
						{
							echo "Dây là chapter cuối cùng";
						}
					}
				}
				break;
			}
		}
		//Lấy nội dung nếu có nhiều chapter
		if($resultStory->mucluc)
		{
			foreach ($resultStory->mucluc as $key => $chapter)
			{
				$query = 'select * from html where url="http://motsach.info/'.$chapter->href.'" and xpath=\'//form[@name="frmEditor"]\'';
				$response = self::$YQL->execute($query);
				$data = $response->results->form->div;
					
				$title = trim($data[0]->p->content);
				$author = $data[1]->p->a->content;
				$category = $data[2]->p->a->content;

				$chapterTitle = sanitize_title($chapter->content);
			
				$content = $this->getChaterContent($data);

				file_put_contents($resultStory->directory."/".$chapterTitle.".txt", implode("\n", $content));
				//echo $chapterTitle."<br/>";
			}
		}
		else
		{
			//Đây là truyện một tập, cần hiện thực sau
			$content = $this->getChaterContent($data);
			$resultStory->mucluc = null;
			$chapterTitle = sanitize_title($data[0]->p->content);
			file_put_contents($resultStory->directory."/mucluc.txt", $data[0]->p->content);
			file_put_contents($resultStory->directory."/".$chapterTitle.".txt", implode("\n", $content));
		}

		return $resultStory->directory;
	}
	//input : Div chính của cái trang đó : $response->results->form->div;
	private function getMucLuc($divs)
	{
		$mucluc = null;
		foreach ($divs as $key => $div) {
			if(isset($div->class) && ($div->class == "right_menu_item"))
			{
				//Làm một dòng trong mục lục
				if(isset($div->a))
				{
					$mucluc[] = $div->a;
				}
			}
		}
		return $mucluc;
	}
	//input : Div chính của cái trang đó : $response->results->form->div;
	private function getChaterContent($divs)
	{
		$Row = null;
		foreach ($divs as $key => $div) {
			if(isset($div->class) && ( ($div->class == "story_text") || ($div->class == "story_poem_text")))
			{
				if(isset($div->div->span))
				{
					//Là dropcap
					if(isset($div->p->a))
					{
						$Row[] = $div->div->span->content.$div->p->a->content;
					}
					else
					{

					}
					$Row[] = $div->div->span->content.$div->p;
				}
				else
				{
					$Row[] = $div->p;
				}
			}
		}
		return $Row;
	}
}
?>