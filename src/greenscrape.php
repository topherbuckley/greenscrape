<?php
require '../vendor/autoload.php';
require_once 'matchHeaders.php';
require_once 'mssafe_csv.php';
require_once 'timer.php';
use Goutte\Client;
set_time_limit(1000);

//code timer
$timer = new timer();
$timer->start();

$page_count = 1;
$idx = 1;
$loop_count = 0;
$total_pages_searched = 0;
$new_headers_full = [];

$cybozu_headers_japanese = [];
$cybozu_headers_japanese = [$idx, $page_count,"取引ステータス","顧客登録","営業担当者","顧客ランク","パートナー登録","パートナー担当者","パートナーランク","企業名","企業名（カナ）","表記","URL","代表者役職","代表者","株式","資本金","従業員数","郵便番号","スペース：住所変換","住所変換","都道府県","市区町村","マンション名など","路線","最寄駅","電話番号","FAX番号","見積フォーマット","見積依頼書","見積発行","発注書","発注請書","支払締日","支払いサイト","金融機関名","金融機関コード","店名","店舗コード","口座種別","口座番号","口座名義","企業特徴","一言コメント","備考","請求備考","実績ありかどうか(0:なし, 1:あり)","最終取引日","プロセスステータス"];
$cybozu_headers_english = [];
$cybozu_headers_english = [$idx, $page_count,"deal_status", "client_registration", "sales_rep", "client_rank", "partner_registration", "partner_rep", "partner_rank", "co_name", "co_name_kana", "co_kk", "co_url", "co_rep_position", "co_rep_name", "co_stocks", "co_capital", "co_employees", "zipcode", "address_button", "address_change", "address_prefecture", "address_city", "address_others", "nearest_line", "nearest_station", "phone", "fax", "estimate_format", "estimate_request", "estimate_issue", "order", "order_confirmation", "payment_closing_date", "payment_terms", "bank_name", "bank_code", "branch_name", "branch_code", "account_type", "account_number", "account_name", "co_feature", "comments", "remarks", "remarks_bill", "has_trade", "last_trading_date", "process_status"];

$result = [];
$result[0] = $cybozu_headers_japanese;
$result[1] = $cybozu_headers_english;

$base_url_to_traverse = 'http://www.green-japan.com/company/';
//$base_url_to_traverse = 'http://www.mei-wu.com/';

/*while ($page_count <= 1538) {
	# code...
}*/

for ($idx = 1; $idx <= 5 ; $idx++) {

	//echo "<br><br>Page Number $page_count, idx = $idx<br><br>";
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
		$result[$page_count+1] = [];
		$result[$page_count+1] = array_pad(array(),count($cybozu_headers_japanese),null) ;

		if ($th_count) {
			$headers = $crawler->filter('table.detail-content-table.js-impression > tr > th')->each(function($node, $i) use($headers) {
				return $headers[$i] = $node->text();
			});
		}
		
		if ($td_count) {
			$data_array = $crawler->filter('table.detail-content-table.js-impression > tr > td')->each(function($node, $i) use($data_array) {
				return $data_array[$i] = $node->text();
			});
		}

		// Check if entry types other than those already handled by cybozu exist. Not sure what to do with them yet, so just echo'ing if found
		$new_headers=array_diff($headers,$new_headers_full);
		
		if ($new_headers != null) {
			echo "<br>new_headers = ";
			print_r($new_headers);
		}

		foreach ($new_headers as $key => $value) {
			$new_headers_full[]= $value;
		}
		
		$new_headers_count = count($new_headers);

		if($new_headers_count != 0){
			echo "$new_headers_count New Headers Found <br><br>";
			//print_r($new_headers_full);
		}

		// to be implemented later
		//$headers = matchHeaders($headers, $cybozu_headers_japanese);	

		//add two slots at begining for index and page count
		array_unshift($headers,"0", "1");
		array_unshift($data_array,"N/A","N/A");
		$data_table = array_combine($headers,$data_array);	

		foreach ($data_table as $key => $value) {

			foreach ($new_headers_full as $key2 => $new_header) {

				if ($key == $new_header) {

					$result[$page_count+1][$key2] = $value;

				}
			}
		}	

		//fill in index and page count
		$result[$page_count+1][0] = $idx;
		$result[$page_count+1][1] = $page_count;
		$result[0][0] = "N/A";
		$result[0][1] = "N/A";
		$result[1][0] = "N/A";
		$result[1][1] = "N/A";

		$page_count++;


	} 
$total_pages_searched++;
$loop_count++;
}

printarray($result);
$unique_headers = implode(",",$new_headers_full);
mssafe_csv('mssafe_greenscrape_results.csv', $result);

$file = fopen("greenscrape_results.csv","w");

for ($row=0; $row < count($result); $row++) { 

	fwrite($file,implode(',',$result[$row]));
	fwrite($file,'/n');

}

fclose($file);

file_put_contents("greenscrape_unique_headers.csv", $unique_headers);
echo "$page_count pages with data. $total_pages_searched total pages scraped";
$timer->stop();
echo "<br>Code Timer = ";
echo $timer->result();
echo "seconds";

function printarray($array){
	print "<pre>";
	print_r($array);
	print "</pre>";
}

?>