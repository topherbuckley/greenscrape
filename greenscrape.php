<?php

require_once 'goutte.phar';
require_once 'matchHeaders.php';
require_once 'mssafe_csv.php';
use Goutte\Client;
set_time_limit(1000);

$cybozu_headers_japanese = [];
$cybozu_headers_japanese = ["取引ステータス","顧客登録","営業担当者","顧客ランク","パートナー登録","パートナー担当者","パートナーランク","企業名","企業名（カナ）","表記","URL","代表者役職","代表者","株式","資本金","従業員数","郵便番号","スペース：住所変換","住所変換","都道府県","市区町村","マンション名など","路線","最寄駅","電話番号","FAX番号","見積フォーマット","見積依頼書","見積発行","発注書","発注請書","支払締日","支払いサイト","金融機関名","金融機関コード","店名","店舗コード","口座種別","口座番号","口座名義","企業特徴","一言コメント","備考","請求備考","実績ありかどうか(0:なし, 1:あり)","最終取引日","プロセスステータス"];
$cybozu_headers_english = [];
$cybozu_headers_english = ["deal_status", "client_registration", "sales_rep", "client_rank", "partner_registration", "partner_rep", "partner_rank", "co_name", "co_name_kana", "co_kk", "co_url", "co_rep_position", "co_rep_name", "co_stocks", "co_capital", "co_employees", "zipcode", "address_button", "address_change", "address_prefecture", "address_city", "address_others", "nearest_line", "nearest_station", "phone", "fax", "estimate_format", "estimate_request", "estimate_issue", "order", "order_confirmation", "payment_closing_date", "payment_terms", "bank_name", "bank_code", "branch_name", "branch_code", "account_type", "account_number", "account_name", "co_feature", "comments", "remarks", "remarks_bill", "has_trade", "last_trading_date", "process_status"];

$result = [];
$result[0] = $cybozu_headers_japanese;
$result[1] = $cybozu_headers_english;

$page_count = 1;
$total_pages_searched = 0;

$base_url_to_traverse = 'http://www.green-japan.com/company/';

for ($idx = 2; $idx <= 3 ; $idx++) {

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
		$new_headers=array_diff($headers,$cybozu_headers_japanese);
		$new_headers_count = count($new_headers);

		if($new_headers_count != 0){
			echo "$new_headers_count Unhandled New Headers Found <br><br>";
		}

		// to be implemented later
		//$headers = matchHeaders($headers, $cybozu_headers_japanese);	

		foreach ($data_table as $key => $value) {

			foreach ($cybozu_headers_japanese as $key2 => $cybozu_header) {

				if ($key == $cybozu_header) {

					$result[$page_count+1][$key2] = $value;

				}
			}
		}	

		$page_count++;

	} 
$total_pages_searched++;
}

mssafe_csv('greenscrape_results.csv', $result);

?>