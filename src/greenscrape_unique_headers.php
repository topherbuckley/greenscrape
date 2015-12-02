<?php
require '../vendor/autoload.php';
require_once 'matchHeaders.php';
require_once 'mssafe_csv.php';
require_once 'timer.php';
use Goutte\Client;
set_time_limit(10000);

if (ob_get_level() == 0) ob_start();

//code timer
$timer = new timer();
$timer->start();

$page_count = 1;
$idx = 1;
$total_pages_searched = 0;
$new_headers_full = [];
$total = 5000;

$base_url_to_traverse = 'http://www.green-japan.com/company/';
$client = new Client();

/*while ($page_count <= 1538) {
	# code...
}*/

for ($idx = 1; $idx <= $total ; $idx++) {

	echo "<br>index = $idx, page_count = $page_count<br>";
	echo "<br>Memory Currently Alloted to PHP: " . memory_get_usage() . "<br>";

	ob_flush();
	flush();


	$url_to_traverse = $base_url_to_traverse . $idx;
	$crawler = $client->request('GET', $url_to_traverse);
	$status_code = $client->getResponse()->getStatus();

	if($status_code==200){
		
		$th_count = $crawler->filter('table.detail-content-table.js-impression > tr > th')->count();
		
		$headers = array();
		$data_array = array();
		$data_table = array();

		if ($th_count) {
			$headers = $crawler->filter('table.detail-content-table.js-impression > tr > th')->each(function($node, $i) use($headers) {
				return $headers[$i] = $node->text();
			});
		}

		// Check if entry types other than those already handled by cybozu exist. Not sure what to do with them yet, so just echo'ing if found
		$new_headers=array_diff($headers,$new_headers_full);

		foreach ($new_headers as $key => $value) {
			$new_headers_full[]= $value;
		}
		
		//$new_headers_count = count($new_headers);
		$page_count++;

	} 
$total_pages_searched++;
}

$unique_headers = implode(",",$new_headers_full);

file_put_contents("../results/greenscrape_unique_headers.csv", $unique_headers);
echo "$page_count pages with data. $total_pages_searched total pages scraped";
$timer->stop();
echo "<br>Code Timer = ";
echo $timer->result();
echo "seconds";
ob_flush();
flush();
ob_end_flush();
