<!doctype html>
 
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Quản lý bot</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
  <script src="javascript/main.js"></script>

  <script src="javascript/jqueryFileTree/jquery.easing.min.js" type="text/javascript"></script>
  <script src="javascript/jqueryFileTree/jqueryFileTree.js" type="text/javascript"></script>
  <link href="javascript/jqueryFileTree/jqueryFileTree.css" rel="stylesheet" type="text/css"/>
  <link href="stylesheet/style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
 
<div id="tabs">
  <ul>
    <li><a href= "#tabs-1" >Đăng bài</a></li>
    <li><a href= "#tabs-2" >Truyện đã download</a></li>
    <li><a href= "#tabs-3" >Bài đã đăng</a></li>
  </ul>
  <div id="tabs-1">
    <div>

    </div>
  </div>
  <div id="tabs-2">
    <div id="storyfiletree" class="storyfiletree"></div>
    <div id="storyDetail">
      <div id="storyInfo">
        <p>Tên truyện : 
          <span id="storyTitle"></span>
        </p>
        <p> Tác giả :
          <span id="storyAuthor"></span>
        </p>
        <p>Thể loại : 
          <span id="storyCategory"></span>
        </p>
      </div>
      <ul id="sortable">
      </ul>
      <button>Bắt đầu đăng</button>
    </div>
  </div>
  <div id="tabs-3">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>
</div>
 
 
</body>
</html>
