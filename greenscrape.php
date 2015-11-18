<?php

require_once 'goutte.phar';
use Goutte\Client;
set_time_limit(1000);

$cybozu_headers = array();
$cybozu_headers[0] =  "レコード番号";
$cybozu_headers[1] =  "営業担当者";
$cybozu_headers[2] =  "企業名";
$cybozu_headers[3] =  "企業名（カナ）";
$cybozu_headers[4] =  "表記";
$cybozu_headers[5] =  "顧客ランク";
$cybozu_headers[6] =  "パートナーランク";
$cybozu_headers[7] =  "代表者";
$cybozu_headers[8] =  "企業URL";
$cybozu_headers[9] =  "資本金";
$cybozu_headers[10] =  "従業員数";
$cybozu_headers[11] =  "市区町村";
$cybozu_headers[12] =  "番地・マンション名など";
$cybozu_headers[13] =  "電話番号";
$cybozu_headers[14] =  "FAX番号";
$cybozu_headers[15] =  "見積フォーマット";
$cybozu_headers[16] =  "見積発行";
$cybozu_headers[17] =  "発注書";
$cybozu_headers[18] =  "発注請書";
$cybozu_headers[19] =  "見積依頼書";
$cybozu_headers[20] =  "支払締日";
$cybozu_headers[21] =  "支払いサイト";
$cybozu_headers[22] =  "企業特徴";
$cybozu_headers[23] =  "一言コメント";
$cybozu_headers[24] =  "備考";
$cybozu_headers[25] =  "請求備考";
$cybozu_headers[26] =  "顧客登録";
$cybozu_headers[27] =  "パートナー登録";
$cybozu_headers[28] =  "取引ステータス";
$cybozu_headers[29] =  "業界";
$cybozu_headers[30] =  "ステータス";
$cybozu_headers[31] =  "作業者";
$cybozu_headers[32] =  "株式";
$cybozu_headers[33] =  "プロセスステータス";
$cybozu_headers[34] =  "路線";
$cybozu_headers[35] =  "最寄駅";
$cybozu_headers[36] =  "実績ありかどうか(0:なし, 1:あり)";
$cybozu_headers[37] =  "最終取引日";
$cybozu_headers[38] =  "都道府県";
$cybozu_headers[39] =  "郵便番号";

$result = array($cybozu_headers);

$page_count = 1;
$total_pages_searched = 0;

$base_url_to_traverse = 'http://www.green-japan.com/company/';

for ($idx = 2; $idx <= 4 ; $idx++) {

	$url_to_traverse = $base_url_to_traverse . $idx;
	$client = new Client();
	$crawler = $client->request('GET', $url_to_traverse);
	$status_code = $client->getResponse()->getStatus();
	$page_title = $crawler->filter('html head title')->text();

	if($status_code==200 && preg_match('/お探しのページは見つかりませんでした./', $page_title)==0){
		
		$th_count = $crawler->filter('th')->count();
		$td_count = $crawler->filter('table.detail-content-table.js-impression > tr > td')->count();
		
		$headers = array();
		$data_array = array();
		$data_table = array();

		if ($th_count) {
			$headers = $crawler->filter('th')->each(function($node, $i) use($headers) {
				return $headers[$i] = $node->text();
			});
		}
		
		if ($td_count) {
			$data_array = $crawler->filter('table.detail-content-table.js-impression > tr > td')->each(function($node, $i) use($data_array) {
				return $data_array[$i] = $node->text();
			});
		}
		
		$data_table = array_combine($headers,$data_array);

		// Check if entry types other than those already handled by cybozu exist. Not sure what to do with them yet, so just echo'ing if found
		$new_headers=array_diff($headers,$cybozu_headers);
		$new_headers_count = count($new_headers);
		if($new_headers_count != 0){
			echo "$new_headers_count New Headers Found <br><br>";
		}

		foreach ($data_table as $key => $value) {

			foreach ($cybozu_headers as $key2 => $cybozu_header) {

				if ($key == $cybozu_header) {
					$result[$page_count][$key2] = $value;
				}else{
					$result[$page_count][$key2] = null;
				}

			}
		}	
		$page_count++;
	} 
$total_pages_searched++;
}

/*foreach ($cybozu_headers as $value) {
	echo "$value<br>";
}*/

for ($idx=0; $idx < $page_count; $idx++) { 
 
	echo implode(", ", $result[$idx]);
	echo "<br><br>";

}

echo "$total_pages_searched Total Pages Searched. <br><br>$page_count Total Pages with Results.";

function matchHeaders($headers, $cybozu_headers) {
    return null;
} 

?>