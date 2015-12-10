<?php
class timer
{
    private $start_time = NULL;
    private $end_time = NULL;

    private function getmicrotime()
    {
      list($usec, $sec) = explode(" ", microtime());
      return ((float)$usec + (float)$sec);
    }

    function start()
    {
      $this->start_time = $this->getmicrotime();
    }

    function stop()
    {
      $this->end_time = $this->getmicrotime();
    }

    function result()
    {
        if (is_null($this->start_time))
        {
            exit('Timer: start method not called !');
            return false;
        }
        else if (is_null($this->end_time))
        {
            exit('Timer: stop method not called !');
            return false;
        }

        return round(($this->end_time - $this->start_time), 4);
    }

    # an alias of result function
    function time()
    {
        $this->result();
    }

}

function printarray($array){
	print "<pre>";
	print_r($array);
	print "</pre>";
}

function matchHeaders($headers, $cybozu_headers_japanese) {

	
$cybozu_headers_japanese = [];
$cybozu_headers_japanese = [$idx, $page_count,"取引ステータス","顧客登録","営業担当者","顧客ランク","パートナー登録","パートナー担当者","パートナーランク","企業名","企業名（カナ）","表記","URL","代表者役職","代表者","株式","資本金","従業員数","郵便番号","スペース：住所変換","住所変換","都道府県","市区町村","マンション名など","路線","最寄駅","電話番号","FAX番号","見積フォーマット","見積依頼書","見積発行","発注書","発注請書","支払締日","支払いサイト","金融機関名","金融機関コード","店名","店舗コード","口座種別","口座番号","口座名義","企業特徴","一言コメント","備考","請求備考","実績ありかどうか(0:なし, 1:あり)","最終取引日","プロセスステータス"];
$cybozu_headers_english = [];
$cybozu_headers_english = [$idx, $page_count,"deal_status", "client_registration", "sales_rep", "client_rank", "partner_registration", "partner_rep", "partner_rank", "co_name", "co_name_kana", "co_kk", "co_url", "co_rep_position", "co_rep_name", "co_stocks", "co_capital", "co_employees", "zipcode", "address_button", "address_change", "address_prefecture", "address_city", "address_others", "nearest_line", "nearest_station", "phone", "fax", "estimate_format", "estimate_request", "estimate_issue", "order", "order_confirmation", "payment_closing_date", "payment_terms", "bank_name", "bank_code", "branch_name", "branch_code", "account_type", "account_number", "account_name", "co_feature", "comments", "remarks", "remarks_bill", "has_trade", "last_trading_date", "process_status"];

$result[0] = $cybozu_headers_japanese;
$result[1] = $cybozu_headers_english;

    foreach ($headers as $key => $value) {

    	if ($headers[$key] == "会社名") {
    		$headers[$key] = "企業名";
    	}
    	
    }

    return $headers;
} 

function mssafe_csv($filepath, $data, $header = array())
{
    if ( $fp = fopen($filepath, 'w') ) {
        $show_header = true;
        if ( empty($header) ) {
            $show_header = false;
            reset($data);
            $line = current($data);
            if ( !empty($line) ) {
                reset($line);
                $first = current($line);
                if ( substr($first, 0, 2) == 'ID' && !preg_match('/["\\s,]/', $first) ) {
                    array_shift($data);
                    array_shift($line);
                    if ( empty($line) ) {
                        fwrite($fp, "\"{$first}\"\r\n");
                    } else {
                        fwrite($fp, "\"{$first}\",");
                        fputcsv($fp, $line);
                        fseek($fp, -1, SEEK_CUR);
                        fwrite($fp, "\r\n");
                    }
                }
            }
        } else {
            reset($header);
            $first = current($header);
            if ( substr($first, 0, 2) == 'ID' && !preg_match('/["\\s,]/', $first) ) {
                array_shift($header);
                if ( empty($header) ) {
                    $show_header = false;
                    fwrite($fp, "\"{$first}\"\r\n");
                } else {
                    fwrite($fp, "\"{$first}\",");
                }
            }
        }
        if ( $show_header ) {
            fputcsv($fp, $header);
            fseek($fp, -1, SEEK_CUR);
            fwrite($fp, "\r\n");
        }
        foreach ( $data as $line ) {
            fputcsv($fp, $line);
            fseek($fp, -1, SEEK_CUR);
            fwrite($fp, "\r\n");
        }
        fclose($fp);
    } else {
        return false;
    }
    return true;
}

function selective_format($site, $key, $value){
        //I need to format columns here. 
        
        
         

    switch ($site) {

        case 'green-japan':
            
            switch ($key) {

                case '代表者氏名':
                    # code...
                    break;

                case '本社所在地':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }

        case '2':
            # code...
            break;

        case '3':
            # code...
            break;
        
        default:
            # code...
            break;
    }

}

function format_entrys($headers2,$data_array2){
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
    return array($headers,$data_array);
}

function check_for_company($headers,$data_array){

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
    return array($headers,$data_array);
}

function split_address($headers,$data_array){
    //within the 本社所在地 column needs to be split into 3 columns; zip / prefecture / city / others

    if (in_array("本社所在地", $headers)) {
        $key = array_search('本社所在地', $headers);
        $address = $data_array[$key];
        array_splice($headers, $key+1, 0, 'others');
        array_splice($headers, $key+1, 0, 'city');
        array_splice($headers, $key+1, 0, 'prefecture');
        array_splice($headers, $key+1, 0, 'zip');
        echo "<br>Original Address = " . $address;
        if (preg_match('/\d\d\d-\d\d\d\d/u', $address, $matches[0])) {
           $zip = $matches[0][0];
           echo "<br>zip = ";
           print_r($zip);
           $address = preg_replace("/.*\d\d\d-\d\d\d\d;?/u", "", $address);
           //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
        }else{
            $zip = "No Data";
        }

        if (preg_match('/([^\s;]*?)[都道府県]/u', $address, $matches[0])) {
           $prefecture = $matches[0][0];
           echo "<br>prefecture = ";
           print_r($prefecture);
           //echo "<br>address before = " . $address;
           $address = preg_replace("/([^\s;]*?)[都道府県]/u", "", $address);
           //echo "<br>address after = " . $address;

           //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
        }else{
            $prefecture = "No Data";
        }

        //the below regex is not perfect, but its the best i can think of at the moment. The flaw would occur if 
        if ($prefecture!="No Data"){
            //need a way to stop regex search if number comes before [市町村区] don't search any longer. Assuming no prefecture/city/ward or anything else has a number in its name.
            if (preg_match('/(.+?市)?(.+?[町村区])?(.+?[村区])?(.+?区)?/u', $address, $matches[0])) {
               $city = $matches[0][0];
               echo "<br>city = $city";
               $address = preg_replace("/(.+?市)?(.+?[町村区])?(.+?[村区])?(.+?区)?/u", "", $address);
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
                $city = "No Data";
            }  
            // I don't think I need this else statement now that I clear the prefecture from the address variable before getting here.  
        }else{
            if (preg_match('/(?<=[都道府県]).*[市区町村]/u', $address, $matches[0])) {
               $city = $matches[0][0];
               echo "<br>city = $city";
               preg_replace('/(?<=[都道府県]).*[市区町村]/u', "", $address);
               //$data_array2[$i] = preg_replace('/[  \t]/', '',$data_array2[$i]);
            }else{
                $city = "No Data";
            }   
        }
        

        if (preg_match('/.*(?=;)?/', $address, $matches[0])) {
           $others = $matches[0][0];
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
    return array($headers,$data_array);
}

?>
