<?php
require_once '/../YQL/YQL.php';
require_once '/../../model/Story.php';
require_once '/../vietnamese-url.php';
?>
<?php
/**
* Class này chỉ viết để đơn giản lấy tạm 300 việc làm ở vietlam42h
*http://hcm.vieclam.24h.com.vn/ajax/ntv_viec_lam_moi_nhat_trang_chu/index/?id_tinh=0&number_items=10000/9/30/0&page=1
*/
class Vietlam24h
{
	public static $YQL;
	public static $instance = null;
	public $domain;
	function __construct()
	{
		self::$YQL = YQL::getInstance();
		$this->domain = "http://hcm.vieclam.24h.com.vn";
	}
	public static function getInstance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new Vietlam24h();
		}
		return self::$instance;
	}
	public function getNewJob()
	{
		$Query = 'select a.href from html where url="http://hcm.vieclam.24h.com.vn/ajax/ntv_viec_lam_moi_nhat_trang_chu/index/?id_tinh=0&number_items=10000/9/30/0&page=1" and xpath=\'//div[@class="ten-chinh"]\'';
		$response = self::$YQL->execute($Query);
		if (isset($response->results->div))
			return $response->results->div;
		return null;
	}
	public function getJob($path)
	{
		$Query = 'select * from html where url="http://hcm.vieclam.24h.com.vn'.$path.'" and xpath=\'//td[contains(@class,"tbInfo")]\'';
		$response = self::$YQL->execute($Query);

		$result = array();

		if (isset($response->results->td))
		{
			$data = $response->results->td;
			$i = 0;

			$content = "";
			foreach ($data as $key => $row) {
				if(isset($row->h3))
				{
					$content.="\n[B][COLOR=#FF0000]".$row->h3."[/COLOR][/B]\n";
				}
				else if($row->class == "tbInfo-row")
				{
					$content.="[B]".$row->strong."[/B]";
				}
				else if($row->class == "tbInfo-row br-L")
				{
					$temp="";
					if(isset($row->a))
					{
						$temp="[url=\"".$row->a->href."\"]".$row->a->content."[/url]\n";
					}
					else if (isset($row->p->content))
					{
						$temp=$row->p->content."\n";
					}
					else
					{
						$temp=$row->p."\n";
					}

					$content.=$temp;

					if (!isset($result["title"]))
					{
						$result["title"] = $temp;
					}
				}
			}
			$result["content"] = $content;
		}
		return $result;
	}
}
?>