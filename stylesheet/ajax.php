<?php
	require_once 'model/Model_VBB.php';
	require_once 'model/Model_Post.php';
	require_once 'class/YQL/YQL.php';
	require_once 'class/crawler/motsach.php';
	require_once 'class/vietnamese-url.php';
	require_once 'class/class.autokeyword.php';

if(isset($_REQUEST['action']))
{
	switch ($_REQUEST['action']) {
		case 'post':
			if(isset($_REQUEST['filepath']))
			{
				echo postStorys($_REQUEST['filepath']);
			}
			break;
		
		default:
			# code...
			break;
	}
}


function postStorys($filepath)
{
	$fiveMuaForum = new ModelVBB('financialprof', 'fathertime', 'http://5mua.vn/');
	$fiveMuaForum->login();

	$content = file_get_contents($filename);
	$post = new Post($storyTitle." : ".$title, $content, generateKeyword($content).",".$storyTitle.",".$storyCategory.",".$storyAuthor);
	$result = $fiveMuaForum->postNewThread($post, 49);
	$threadLink["title"] =$title;
	$threadLink["redirectUrl"] = $result["redirectUrl"];
	return json_encode($threadLink);
}
?>