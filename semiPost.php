<meta charset="utf-8" />
<?php
	require_once 'model/Model_VBB.php';
	require_once 'model/Model_Post.php';
	require_once 'class/YQL/YQL.php';
	require_once 'class/crawler/motsach.php';
	require_once 'class/vietnamese-url.php';
	require_once 'class/class.autokeyword.php';

	//Đăng bằng cái này thì rất nhanh, nhưng mà giao diện thì không được thân thiện như cái kia
///////////////////////////////////////// Var ///////////////////////////////////////
$storyDir = null;
/////////////////////////////////////////END Var ////////////////////////////////////




///////////////////////////////////////// Tải truyện về ///////////////////////////////////////

	$motsach = MotSach::getInstance();

	$storyAddressList = $motsach->getAllStoryOfCategory('kiem_hiep');
	//$storyAddressList = $motsach->getAllStoryOfCategory('truyen_ngan');
	//
	foreach ($storyAddressList as $pagekey => $page) {
		foreach ($page as $storykey => $story) {
			writelog("\n\ngetStory : ".$story["href"]);
			//$storyDir = $motsach->getStory($story["href"]."&chapter=000");
			$storyDir = $motsach->getStory("story.php?story=12_chien_cong_cua_hercule__thierry_lefevre&chapter=000");
			Echo $storyDir."<br/>";
			if($storyDir!=false)
			{
				writelog("\n\npostToForum : ".$storyDir);
				postToForum($storyDir);
			}
			break;
		}
		break;
	}
///////////////////////////////////////// kết thúc Tải truyện về ///////////////////////////////////////
///////////////////////////////////////// Đăng lên diễn đàn ///////////////////////////////////////
function postToForum($storyDir)
{
	if($storyDir)
	{
		$fiveMuaForum = new ModelVBB('phoenixsinh9x', 'fathertime', 'http://diendan.zing.vn/');
		$fiveMuaForum->login();

		$file = fopen($storyDir."/info.txt","r");

		$storyTitle = fgets($file);
		$storyAuthor = fgets($file);
		$storyCategory = fgets($file);

		fclose($file);

		$threadLink = null;
		$file = fopen($storyDir."/mucluc.txt","r");
		$i = 0;
		while(! feof($file))
		{
			$title = fgets($file);
			$filename = $storyDir.'/'.sanitize_title($title).".txt";

			$content = file_get_contents($filename)."\nĐọc thêm tại : [URL=\"http://muatocroi.com\"]Mùa Tóc Rối[/URL]";
			$post = new Post($storyTitle." : ".$title, $content, generateKeyword($content).",".$storyTitle.",".$storyCategory.",".$storyAuthor);

			writelog("\t\t\t\tposting : ".$title);

			$result = $fiveMuaForum->postNewThread($post, 2628);
			$threadLink[$i]["title"] =$title;
			$threadLink[$i]["redirectUrl"] = $result["redirectUrl"];
			$i++;
			echo $result["redirectUrl"]."<br />";
			sleep(60);//second
		}
		fclose($file);
		if($threadLink)
		{
			$postcontent = "Bìa truyện\nTên truyện : ".$storyTitle."\nTác giả : ".$storyAuthor."\n Thể loại : ".$storyCategory."\n\n\nMục lục\n";
			foreach ($threadLink as $title => $threadURL) {
				$postcontent.="[URL=\"".$threadURL["redirectUrl"]."\"]".$threadURL["title"]."[/URL]\n";
			}
		}
		if (count($threadLink) > 1)
		{
			$post = new Post($storyTitle." : Mục lục", $postcontent, $storyTitle.", mục lục, ".$storyCategory.",".$storyAuthor);
			$result = $fiveMuaForum->postNewThread($post, 191,true);
			echo $result["redirectUrl"]."<br />";
		}
	}
	else
	{
		echo "Không có thư mục truyện";
	}
}
/////////////////////////////////////////Kết thúc Đăng lên diễn đàn ///////////////////////////////////////

function generateKeyword($content)
{
	$params['content'] = $content;

	$params['min_2words_length'] = 2;  //minimum length of words for 2 word phrases
	$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
	$params['min_2words_phrase_occur'] = 18; //minimum occur of 2 words phrase

	$keyword = new autokeyword($params, "UTF-8");
	$result = $keyword->parse_2words();
	return $result;
}
function writelog($content)
{
	file_put_contents('data/log.txt', "\n".$content, FILE_APPEND);
}
?>