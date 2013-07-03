<meta charset="utf-8" />
<?php
	require_once 'model/Model_VBB.php';
	require_once 'model/Model_Post.php';
	require_once 'class/YQL/YQL.php';
	require_once 'class/crawler/Vietlam24h.php';
	require_once 'class/vietnamese-url.php';
	require_once 'class/class.autokeyword.php';

	//Đăng bằng cái này thì rất nhanh, nhưng mà giao diện thì không được thân thiện như cái kia
///////////////////////////////////////// Var ///////////////////////////////////////
$storyDir = null;
/////////////////////////////////////////END Var ////////////////////////////////////




///////////////////////////////////////// Tải truyện về ///////////////////////////////////////

	$fiveMuaForum = new ModelVBB('financialprof', 'fathertime', 'http://5mua.vn/');
	$fiveMuaForum->login();

	$Vietlam24h = Vietlam24h::getInstance();
	$newJobs = $Vietlam24h->getNewJob();

	postToForum($newJobs, $Vietlam24h);
///////////////////////////////////////// kết thúc Tải truyện về ///////////////////////////////////////
///////////////////////////////////////// Đăng lên diễn đàn ///////////////////////////////////////
function postToForum($newJobs, $Vietlam24h)
{
	$fiveMuaForum = new ModelVBB('financialprof', 'fathertime', 'http://5mua.vn/');
	$fiveMuaForum->login();
	foreach ($newJobs as $key => $Job) {
		$content = $Vietlam24h->getJob($Job->a->href);
		$post = new Post($content["title"], $content["content"]);
		$result = $fiveMuaForum->postNewThread($post, 50);
		echo $result["redirectUrl"]."<br/>";
	}
}
/////////////////////////////////////////Kết thúc Đăng lên diễn đàn ///////////////////////////////////////

function writelog($content)
{
	file_put_contents('data/log.txt', "\n".$content, FILE_APPEND);
}
?>