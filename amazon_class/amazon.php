<?php
class amazon
{
	/*
	ISBNもしくは名前で本を検索する
	引数：ISBNもしくは名前
	戻り値：それについての情報(xml)
	*/
	function search($keyword) {
		$AccesskeyID = 'AKIAIMRCA3WLKD3RSGRQ';
		$SecretAccessKey = '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';
		
		$url = 'http://ecs.amazonaws.jp/onca/xml';
		
		$params = array('AWSAccessKeyId' => $AccesskeyID,
				'Service' => 'AWSECommerceService',
				'Version' => '2011-08-01'
				'Operation' => 'ItemSearch',
				'ResponseGroup' => 'ItemAttributes,Images',
				'SearchIndex' => $SearchIndex,
				'Keywords' => 'Books',
				'AssociateTag' => 'appasoc-22',
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
		
		return  simplexml_load_file($url);
	}
}
?>

