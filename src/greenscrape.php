<?php
require '../vendor/autoload.php';
require_once 'utilities.php';
use Goutte\Client;

set_time_limit(100000);

if (ob_get_level() == 0) ob_start();

header('Content-Type: text/html; charset=utf-8');

//code timer
$timer = new timer();
$timer->start();

$page_count = 0;
$idx = 1;
$total_pages_searched = 0;
$new_headers_full = [];
$total = 18;//4100;

$base_url_to_traverse = 'http://www.green-japan.com/company/';
$result = [];
$client = new Client();

/*while ($page_count <= 1538) {
	# code...
}*/

//initialize headers array. page_count is primary key. Index is the number specifying which company in the URL, e.g. the index for the following URL is 3 --> www.green-japan.com/company/3
$headers = array();

for ($idx = 1; $idx <= $total ; $idx++) {

	//echo "<br>index = $idx, page_count = $page_count<br>";
	//echo "<br>Memory Currently Alloted to PHP: " . memory_get_usage() . "<br>";

	//ob_flush();
	//flush();

	$url_to_traverse = $base_url_to_traverse . $idx;
	$crawler = $client->request('GET', $url_to_traverse);
	$status_code = $client->getResponse()->getStatus();

	//$page_title = $crawler->filter('html head title')->text();
	//if($status_code==200 && preg_match('/お探しのページは見つかりませんでした./', $page_title)==0){
	
	if($status_code==200){
		
		$th_count = $crawler->filter('table.detail-content-table.js-impression > tr > th')->count();
		$td_count = $crawler->filter('table.detail-content-table.js-impression > tr > td')->count();
		
		//Initialize data_array. data_array represents all the data (no headers) for a particular URL.
		$data_array = [];
		$data_array = [$page_count+1, $idx, "green-japan"];
		//$new_headers_full = ["page_count", "index", "site"];
		$headers = ["page_count", "index", "site"];
		$data_array2 = [];
		$headers2 = [];

		/*-Initialize data_table. data_table represents the 2d array with both headers and data_array values. 
		---This is so the headers can be used as a primary key to set up the results array later. 
		---Without this if for example the 3rd header of page_count=1 was different than the 3rd header of page_count=2 
		---the results table would not align between rows. */
		$data_table = [];


		if ($th_count) {
			$headers2 = $crawler->filter('table.detail-content-table.js-impression > tr > th')->each(function($node, $i) use($headers2) {
				return $headers2[$i] = $node->html();
			});
		}
		
		if ($td_count) {
			$data_array2 = $crawler->filter('table.detail-content-table.js-impression > tr > td')->each(function($node, $i) use($data_array2) {
				
				$data_array2[$i] = $node->html();

				if (preg_match('/<br>/', $data_array2[$i])) {
	               
	               return $data_array2[$i] = preg_replace('/<br>/', ';',$data_array2[$i]);

	            }	
	            else{

	            	return $data_array2[$i] = $node->text();

	            }			
				
			});
		}

		//Append data and header elements to the first 3 indexing elements
		for ($i=0; $i < count($data_array2); $i++) { 

			//remove html from individual values. 
			if (preg_match('/<.+>/', $data_array2[$i])) {
               $data_array2[$i] = preg_replace('/<.*?>/', '',$data_array2[$i]);
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }

			//remove line breaks from individual values. 
			if (preg_match('/[\n\r]/', $data_array2[$i])) {
               $data_array2[$i] = preg_replace('/[\n\r]/', '',$data_array2[$i],1);
               $data_array2[$i] = preg_replace('/[\n\r]/', ';',$data_array2[$i]);
               $data_array2[$i] = preg_replace('/;(\s+)?$/', '',$data_array2[$i],1);
            }

			//remove extra whitespace from individual values. 
			if (preg_match('/[ \t]/', $data_array2[$i])) {
               $data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }

			$data_array[$i+3] = $data_array2[$i];
			$headers[$i+3] = $headers2[$i];
		}

		//add in header columns for 株式会社_flag.  
		//It seems like a waste of time to check this every time...wonder if I can speed this up somehow??
		if (in_array("会社名", $headers)) {
			$key = array_search('会社名', $headers);
			array_splice($headers, $key+1, 0, '株式会社_flag');

			//Within the 会社名 column needs to remove 株式会社 and enter 1,2,3 as a flag in an additional column for 1=前株, 2=後株, 3=株なし
            $company_name = $data_array[$key];

            if (mb_substr($company_name, 0, 4)=='株式会社') {
                $company_name = mb_substr($company_name, 4);
                $flag = 1;
            }
            elseif (mb_substr($company_name, -4)=='株式会社') {
                $company_name = mb_substr($company_name, 0,-4);
				$flag = 2;
            }
            else{
                $flag = 3;
            }

			array_splice($data_array, $key+1, 0, $flag);

		}

		//within the 本社所在地 column needs to be split into 3 columns; zip / prefecture / city / others

		if (in_array("本社所在地", $headers)) {
			$key = array_search('本社所在地', $headers);
			$address = $data_array[$key];
            array_splice($headers, $key+1, 0, 'others');
            array_splice($headers, $key+1, 0, 'city');
            array_splice($headers, $key+1, 0, 'prefecture');
            array_splice($headers, $key+1, 0, 'zip');

            if (preg_match('/\d\d\d-\d\d\d\d/', $address, $matches[0])) {
               $zip = $matches[0];
               echo "<br>zip = ";
               print_r($zip);
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
            	$zip = "No Data";
            }

            if (preg_match('/([^\s;]*?)[都道府県]/u', $address, $matches[0])) {
               $prefecture = $matches[0];
               echo "<br>prefecture = ";
               print_r($prefecture);
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
            	$prefecture = "No Data";
            }

            if (preg_match('/not coded yet/', $address, $matches[0])) {
               $city = $matches[0];
               echo "<br>city = $city<br>";
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
            	$city = "No Data";
            }

            if (preg_match('/not coded yet/', $address, $matches[0])) {
               $others = $matches[0];
               echo "<br>others = $others<br>";
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
            	$others = "No Data";
            }

            array_splice($data_array, $key+1, 0, $others);
            array_splice($data_array, $key+1, 0, $city);
            array_splice($data_array, $key+1, 0, $prefecture);
            array_splice($data_array, $key+1, 0, $zip);

		}

		//Find new headers and add to compounding array $new_headers_full
		$new_headers=array_diff($headers,$new_headers_full);

		/*for ($i=0; $i < count($new_headers); $i++) { 
			$new_headers_full[$i+3]= $new_headers[$i];
		}*/

		$new_headers_full = array_merge($new_headers_full,$new_headers);

		//result represents the 2D array which will include headers and data, and be printed to csv. 
		//Not sure if pushing this full array every loop is quicker than an if statement to check new header count. ??
		$result[0] = $new_headers_full;
		$result[$page_count+1] = [];
		$result[$page_count+1] = array_pad(array(),count($new_headers_full),null) ;


		//add two slots at begining for index and page count, and site name
		//array_unshift($headers,"0", "1", "2");
		//array_unshift($data_array,"N/A","N/A","N/A");
		$data_table = array_combine($headers,$data_array);	

		foreach ($data_table as $key => $value) {

			foreach ($new_headers_full as $key2 => $new_header) {

				if ($key == $new_header) {

					//the following line REQUIRES that the site name remain in column 3 (index 2). 
					$site = $result[$page_count+1][2];
					//list($value2,$flag) = selective_format($site, $key, $value);

					$result[$page_count+1][$key2] = $value;

				}
			}
		}	

		$page_count++;

	} 
$total_pages_searched++;
}

//$unique_headers = implode(",",$new_headers_full);
mssafe_csv('../results/mssafe_greenscrape_results.csv', $result);

$file = fopen("../results/greenscrape_results.csv","w");

for ($row=0; $row < count($result); $row++) { 

	fwrite($file,implode(',',$result[$row]));
	fwrite($file,'/n');

}

fclose($file);

//file_put_contents("greenscrape_unique_headers.csv", $unique_headers);
echo "Successfully scraped " . $page_count . " pages with data. $total_pages_searched total pages scraped";
$timer->stop();
echo "<br>Code Timer = ";
echo $timer->result();
echo "seconds";
ob_flush();
flush();
ob_end_flush();
?>