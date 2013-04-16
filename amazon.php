<?php
class amazon
{
	/*
	ISBNもしくは名前で本を検索する
	引数：ISBNもしくは名前
	戻り値：それについての情報(xml)
	*/
	function search($keyword) {
		$AccesskeyID = '';
		$SecretAccessKey = '';
		
		$url = 'http://ecs.amazonaws.jp/onca/xml';
		
		$params = array('AWSAccessKeyId' => $AccesskeyID,
				'Service' => 'AWSECommerceService',
				'Version' => '2011-08-01',
				'Operation' => 'ItemSearch',
				'ResponseGroup' => 'ItemAttributes,Images',
				'SearchIndex' => 'Books',
				'Keywords' => $keyword,
				'AssociateTag' => 'dummy',
				'Timestamp' => gmdate('Y-m-d\TH:i:s\Z') );
		
		ksort($params);
		
		// パラメータを収集し、結合、エンコード
		$canonical_string = '';
		foreach ($params as $k => $v) {
			$canonical_string .= '&'.rawurlencode($k).'='.rawurlencode($v);
		}
		$canonical_string = substr($canonical_string, 1);
		
		// シグネチャ生成
		// - 規定の文字列フォーマットを作成
		// - HMAC-SHA256 を計算
		// - BASE64 エンコード
		$baseurl = $url;
		$parsed_url = parse_url($baseurl);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $SecretAccessKey, true));
    		
		// ハッシュ済みパラメータとシグネチャを結合
		$url = $baseurl.'?'.$canonical_string.'&Signature='.rawurlencode($signature);
		
		$xml = simplexml_load_file($url);
		
		$data[] = $xml->Items->Item->ItemAttributes->ISBN;
		$data[] = $xml->Items->Item->DetailPageURL;
		$data[] = $xml->Items->Item->LargeImage->URL;
		$data[] = $xml->Items->Item->ItemAttributes->Author;
		$data[] = $xml->Items->Item->ItemAttributes->PublicationDate;
		$data[] = $xml->Items->Item->ItemAttributes->Title;
		
		return $data;
	}
}
?>

