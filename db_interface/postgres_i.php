<?php
/*
	PostgreSQL Interface
		interface：５行目〜
		class本文：７０行目〜
*/
interface db_connector
{
	function db_connect();
	
	/*
	テーブル作成用
	引数：無
	戻り値：Error message
	*/
	function create_table();
	
	function db_close();
	
	/*
	本の情報をデータベースに登録する
	引数：登録するデータの配列
	戻り値：正常完了：true, 異常終了：false
	*/
	function setbook(array $book);
	
	/*
	本の冊数をアップデートする
	引数：ISBN
	戻り値：正常完了；true, 異常終了：false
	*/
	function addbook($isbn);
	
	/*
	データベースから本の情報を取り出す
	引数：検索する本のISBNコード
	戻り値：検索結果の配列
	*/
	function find($isbn);
	
	/*
	本を削除
	引数：isbn
	戻り値：正常完了：true, 異常終了：false
	*/
	function rmbook($isbn);
	
	/*
	本を貸出
	引数：isbn,user id
	戻り値：正常完了：true, 異常終了：false
	*/
	function borrow($isbn, $user);
	
	/*
	本の返却
	引数：isbn,user id
	戻り値：正常完了：true, 異常終了：false
	*/
	function repayment($isbn, $user);
	
	/*
	貸出状況一覧出力
	引数：無
	戻り値：貸出一覧配列
	*/
	function lending();
}

class postgres_i implements db_connector
{
	private $dbconn_book;
	private $dbconn_borrow;
	
	function db_connect(){
		$dbconn_book = pg_connect("dbname=bookshelf");
		$dbconn_borrow = pg_connect("dbname=borrows");
		if(! $dbconn_book | $dbconn_borrow){
			return false;
		}else{
			return true;
		}
	}
	
	function create_table(){
		$query = "CREATE TABLE bookshelf(";
		$query .= "ISBN varchar(13) PRIMARY KEY,";
		$query .= "URL varchar(500),";
		$query .= "Image varchar(100),";
		$query .= "Author varchar(100),";
		$query .= "PubDate varchar(10),";
		$query .= "Title varchar(100),";
		$query .= "amount int);";
		pg_query($dbconn_book, $query);
		$error = pg_last_error($dbconn_book)."\n\n";
		
		$query = "CREATE TABLE borrows(";
		$query .= "ID varchar(20),";
		$query .= "ISBN varchar(13) UNIQUE,";
		$query .= "bDate date,";
		$query .= "rDate date);";
		pg_query($dbconn_borrow, $query);
		$error .= pg_last_error($dbconn_borrow)."\n";
		
		return $error;
	}
	
	function db_close(){
		pg_close($dbconn_book);
		pg_close($dbconn_borrow);
	}
	
	function setbook(array $book){
		//ここに処理が来た時点で未登録書籍であること
		$query = "INSERT INTO bookshelf(ISBN, URL, Image, Author, PubDate, Title, amount)";
		$query .= "VALUES ('". $book->Items->Item->ItemAttributes->ISBN ."',";
		$query .= "'". $book->Items->Item->DetailPageURL ."',";
		$query .= "'". $book->Items->Item->LargeImage->URL ."',";
		$query .= "'". $book->Items->Item->ItemAttributes->Author ."',";
		$query .= "'". $book->Items->Item->ItemAttributes->PublicationDate ."',";
		$query .= "'". $book->Items->Item->ItemAttributes->Title ."',";
		$query .= "0);";
		
		pg_query($dbconn_book, $query);
		return pg_last_error($dbconn_book);
	}
	
	function addbook($isbn){
		//ここに処理が来た時点で登録済みの書籍であること
		$data = find($isbn);
		//冊数加算
		$data[6] ++;
		
		pg_query($dbconn_book, "UPDATE bookshelf SET amount={$data[6]} WHERE ISBN='{$isbn}';");
		return pg_last_error($dbconn_book);
	}
	
	function find($isbn){
		$value = pg_query($dbconn_book, "SELECT * FROM bookshelf WHERE ISBN='{$isbn}';");
		if(pg_num_rows($value) == 0) return 0;	//検索結果NULL？
		
		return pg_fetch_row($value, 0);
	}
	
	function rmbook($isbn){
		pg_query($dbconn_book, "DELETE FROM bookshelf WHERE ISBN='{$isbn}';");
		return pg_last_error($dbconn_book);
	}
	
	function borrow($isbn, $user){
		//ここに処理が来た時点で登録済みかつ、貸出中でない書籍であること
		$query = "INSERT INTO borrows(ID, ISBN, bDate, rDate)";
		$query .= "VALUES ('{$user}', '{$isbn}',";
		$query .= "'". date("Y-m-d") ."', NULL);";
		
		pg_query($dbconn_borrow, $query);
		return pg_last_error($dbconn_borrow);
	}
	
	function repayment($isbn, $user){
		$rdate = date("Y-m-d");
		pg_query($dbconn_book, "UPDATE borrows SET rDate='{$rdate}' WHERE ISBN='{$isbn}' AND ID='{$user}';");
		return pg_last_error($dbconn_book);
	}
	
	function lending(){
		$value = pg_query($dbconn_borrow, "SELECT ID, ISBN, bDate FROM borrows WHERE rDate=NULL");
		if(pg_num_rows($value) == 0) return 0;	//検索結果NULL？
		
		for($i=0; $i<pg_num_rows($value); $i++){
			data[] = pg_fetch_row($value, $i);
		}
		return data;
	}
}

