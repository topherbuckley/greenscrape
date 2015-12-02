<?php
function matchHeaders($headers, $cybozu_headers_japanese) {

    foreach ($headers as $key => $value) {

    	if ($headers[$key] == "会社名") {
    		$headers[$key] = "企業名";
    	}
    	
    }

    return $headers;
} 
?>