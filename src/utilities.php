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
?>
