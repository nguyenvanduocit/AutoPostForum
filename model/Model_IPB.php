<?php

require_once '/../class/curl.class.php';
require_once 'Model_Post.php';
class ModelVBB {

 
 private $auto;

 private $username;

 private $password;
 
 private $domain;
 
 private $login;
 
 private $newThread;
 
 private $privateMessage;

 public function __construct($username, $password, $domain) {
	 $this->username = $username;
	 $this->password = $password;
	 $this->domain = $domain;
	 $this->auto = curlClass::getInstance();
	 $this->auto->setCookie(parse_url($domain, PHP_URL_HOST).'.txt');
 }

 
 public function login() {
	 $this->login = array(
		 's' => '',
		 'securitytoken' => 'guest',
		 'do' => 'login',
		 'vb_login_username' => $this->username,
		 'vb_login_password' => $this->password,
		 'vb_login_md5password' => md5($this->password),
		 'vb_login_md5password_utf' => md5($this->password)
	 );
	 return $this->auto->sendPost($this->login, $this->domain . '/index.php?app=core&module=global&section=login');
 }

 
 public function postNewThread($post, $forumID, $isStick=false) {
	 $result = $this->auto->fetchURL($this->domain . 'newthread.php?do=newthread&f=' . $forumID);
	 preg_match_all('#name="posthash" value="(.*?)"#s', $result, $a); 
	 preg_match_all('#name="poststarttime" value="(.*?)"#s', $result, $b); 
	 preg_match_all('#name="loggedinuser" value="(.*?)"#s', $result, $c); 
	 preg_match_all('#name="securitytoken" value="(.*?)"#s', $result, $d); 
	 $this->newThread = array(
		 'subject' => $post->subject,
		 'message' => $post->message,
		 'stickunstick'=>($isStick?"1":"0"),
		 'taglist'=>$post->taglist,
		 'signature'=>'1',
		 's' => '',
		 'f' => $forumID,
		 'do' => 'postthread',
		 'posthash' => $a[1][0],
		 'poststarttime' => $b[1][0],
		 'loggedinuser' => $c[1][0],
		 'securitytoken' => $d[1][0]
	 );
	 return $this->auto->sendPost($this->newThread, $this->domain . 'newthread.php?do=postthread&f=' . $forumID);
 }

public function editThread($post,  $postID, $reason = "")
{
	 $result = $this->auto->fetchURL($this->domain . 'editpost.php?do=editpost&p=' . $postID);
	 preg_match_all('#name="posthash" value="(.*?)"#s', $result, $a); 
	 preg_match_all('#name="poststarttime" value="(.*?)"#s', $result, $b); 
	 preg_match_all('#name="securitytoken" value="(.*?)"#s', $result, $d); 
	 $this->newThread = array(
	 	'reason'	=>	$reason,
		 'title' 	=> 	$post->subject,
		 'message' 	=> 	$post->message,
		 's' 		=> 	'',
		 'p' 		=> 	$postID,
		 'do' 		=> 	'updatepost',
		 'posthash'	=> 	$a[1][0],
		 'poststarttime' => $b[1][0],
		 'securitytoken' => $d[1][0]
	 );
	 return $this->auto->sendPost($this->newThread, $this->domain . 'editpost.php?do=editpost&p=' . $postID);
}
 
 public function postPrivateMessge($title, $message, array $list) {
	 $result = $this->auto->fetchURL($this->domain . 'private.php?do=newpm');
	 preg_match_all('#name="securitytoken" value="(.*?)"#s', $result, $a); 
	 $this->privateMessage = array(
		 'recipients' => implode(';', $list),
		 'title' => $title,
		 'message' => $message,
		 's' => '',
		 'securitytoken' => $a[1][0],
		 'do' => 'insertpm',
		 'pmid' => '',
		 'forward' => ''
	 );
	 $this->auto->sendPost($this->privateMessage, $this->domain . 'private.php?do=insertpm&pmid=');
 }

}

//// Initialize object
//$vbb = new VBB('username', 'password', 'http://www.example.com/');
//// Login to your account
//$vbb->login();
//// Send a new thread
//$vbb->postNewThread('I am auto post engine', 'Hello all! I am auto post engine', 79);
//// Send a new private message to other users
//$vbb->postPrivateMessge('Hello!', 'I am very happy when we are friend', array('yumyum', 'uitstudent', 'kitty'));
?>