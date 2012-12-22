<?php
/*
	PostgreSQL Interface
*/
interface db_connector
{
	function db_connect();
	function create_table();
	function db_close();
	function setbook(array $book);
	function find($isbn);
	function rmbook($isbn);
	function borrow($isbn, $user);
	function repayment($isbn, $user);
	function lending();
}

class postgres_i implements db_connector
{
	private $dbconn_book;
	private $dbcon_borrow;
	
	function db_connect(){
		$dbconn_book = pg_connect("dbname=book");
		$dbconn_borrow = pg_connect("dbname=borrow");
		if(! $dbconn_book | $dbconn_borrow){
			return false;
		}else{
			return true;
		}
	}
	
	/*
	テーブル作成用
	引数：無
	戻り値：Error message
	*/
	function create_table(){
		$query = "CREATE TABLE";
		$query .= "";
		pg_query($dbconn_book, $query);
		$error = pg_last_error($dbconn_book)."\n\n";
		
		$query = "";
		$query .= "";
		pg_query($dbconn_borrow, $query);
		$error .= pg_last_error($dbconn_borrow)."\n";
		
		return $error;
	}
	
	function db_close(){
		pg_close($dbconn_book);
		pg_close($dbconn_borrow);
	}
	
	/*
	本の情報をデータベースに登録する
	引数：登録するデータの配列
	戻り値：正常完了：true, 異常終了：false
	*/
	function setbook(array $book){
			
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
	引数：isbn,user id
	戻り値：正常完了：true, 異常終了：false
	*/
	function borrow($isbn, $user){
		
	}
	
	/*
	本の返却
	引数：isbn,user id
	戻り値：正常完了：true, 異常終了：false
	*/
	function repayment($isbn, $user){
		
	}
	
	/*
	貸出状況一覧出力
	引数：無
	戻り値：貸出一覧配列
	*/
	function lending(){
		
	}
}

