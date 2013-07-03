$(function() {
////////UI
	$("#tabs").tabs();
	$(  "#sortable"  ).sortable();
   	$(  "#sortable"  ).disableSelection();
//////end ui

	$('#storyfiletree').fileTree({
		root: 'data/',
		script: 'jqueryFileTree.php',
		onIsStoryDirCallback : onIsStoryDirCallback
	});
});
function onIsStoryDirCallback(data)
{
	$.post("jqueryFileTree.php", { storydir: data , action : "getFile"}, function(data) {
		console.log(data);
		$('#sortable').empty();
		$('#storyTitle').html(data.storyTitle);
		$('#storyAuthor').html(data.storyAuthor);
		$('#storyCategory').html(data.storyCategory);
		for (var i = 0; i < data.files.length; i++) {
			$('#sortable').append('<li class="ui-state-default" id="'+data.files[i].file+'" href="'+data.files[i].file+'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+data.files[i].title+'</li>');
		};
	},"json");
}
function postToForum(chapterList)
{
	$.post("jqueryFileTree.php", { filepath: data , action : "post"}, function(data) {
		
	},"json");
}