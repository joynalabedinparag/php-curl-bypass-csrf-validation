<?php
	$username = "site-username";
	$password = "site-password";
	$url = "https://site-url.com";
	$token_cookie= realpath("test.txt");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $token_cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $token_cookie);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	
	if (curl_errno($ch)) die(curl_error($ch));
	libxml_use_internal_errors(true);
	$dom = new DomDocument();
	$dom->loadHTML($response);
	libxml_use_internal_errors(false);
	$tokens = $dom->getElementsByTagName("input");
	for ($i = 0; $i < $tokens->length; $i++) {
		$meta = $tokens->item($i);
		if($meta->getAttribute('name') == 'csrfmiddlewaretoken')
			$t = $meta->getAttribute('value');
	}
	if($t) {
		$csrf_token = file_get_contents(realpath("another-cookie.txt"));
		$postinfo = "username=".$username."&password=".$password."&csrfmiddlewaretoken=".$t;
		  
		$headers = array();
		
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";
	
		$headers[] = "X-CSRF-Token: $t";
		$headers[] = "Cookie: $token_cookie";

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		
		curl_setopt($ch, CURLOPT_COOKIEJAR, $token_cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $token_cookie);
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 260);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		ob_start();
		$html = curl_exec($ch);
		$result = curl_getinfo($ch);
		ob_get_clean();
		echo "<pre>";
		print_r($result);
		print($html);
		if (curl_errno($ch)) print curl_error($ch);
		curl_close($ch); 
	}
	
?>	