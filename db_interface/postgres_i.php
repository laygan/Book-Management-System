<?php
class postgres_i
{
	$db_name, $dbconn;
	function __construct($tmp){
		$db_name = $tmp;
	}
	
	function db_connect(){
		$dbconn = pg_connect("dbname=mary");
		if(! $dbconn){
			return false;
		}else{
			return true;
		}
	}
	
	/*
	本の情報をデータベースに登録する
	引数：登録するデータの配列
	戻り値：正常完了：true, 異常終了：false
	*/
	function setbook($book){
		
	}
	
	/*
	データベースから本の情報を取り出す
	引数：検索する本のISBNコード
	戻り値：検索結果の配列
	*/
	function find($isbn){
		
	}
	
	/*
	本を削除
	引数：isbn
	戻り値：正常完了：true, 異常終了：false
	*/
	function rmbook($isbn){
		
	}
	
	/*
	本を貸出
	引数：isbn
	戻り値：正常完了：true, 異常終了：false
	*/
	function borrow($isbn){
		
	}
	
	/*
	本の返却
	引数：isbn
	戻り値：正常完了：true, 異常終了：false
	*/
	function repayment($isbn){
		
	}
	
	/*
	貸出状況一覧出力
	引数：無
	戻り値：貸出一覧配列
	*/
	function lending(){
		
	}
}

