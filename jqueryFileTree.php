<?php
require_once 'class/vietnamese-url.php';
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//
$root = '';

if(isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];

	if (isset($_REQUEST['dir']))
	{
		$_REQUEST['dir'] = urldecode($_REQUEST['dir']);
	}

	if($action == "getdirlist")
	{
		echo getList();
	}
	else if($action == "getFile")
	{
		if(isset($_REQUEST['storydir']))
			echo getStoryInfo($_REQUEST['storydir']);
	}
}
function getStoryInfo($filepath)
{
	$returnData = array();
	$file = fopen($filepath."/info.txt","r");

	$returnData["storyTitle"] = fgets($file);
	$returnData["storyAuthor"] = fgets($file);
	$returnData["storyCategory"] = fgets($file);

	fclose($file);

	$file = fopen($filepath."/mucluc.txt","r");
	$i = 0;
	while(!feof($file))
	{
		$title = fgets($file);
		$returnData["files"][$i]["title"] = $title;
		$returnData["files"][$i]["file"] = $filepath.'/'.sanitize_title($title).".txt";;
		$i++;
	}
	fclose($file);
	return json_encode($returnData);
}

function getList()
{
	$root = '';
	$returnData["content"] = "";

	if( file_exists($root . $_POST['dir']) ) {
		$files = scandir($root . $_POST['dir']);
		natcasesort($files);
		if( count($files) > 2 ) { /* The 2 accounts for . and .. */
			$returnData["content"] = "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
			// All dirs
			foreach( $files as $file ) {
				if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
					$returnData["content"] .= "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
				}
			}
			// All files
			foreach( $files as $file ) {
				if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
					$ext = preg_replace('/^.*\./', '', $file);
					if($file == "info.txt")
					{
						$returnData["isStoryDir"] = 1;
					}
					$returnData["content"] .= "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
				}
			}

			$returnData["content"] .="</ul>";	
			return json_encode($returnData);
		}
	}
}


?>