<?php
/*
	PostgreSQL Interface
		interface：５行目〜
		class本文：７０行目〜
*/
interface db_connector
{
	function db_connect();
	
	function db_close();
	
	/*
	本の情報をデータベースに登録する
	引数：登録するデータの配列
	戻り値：正常完了：true, 異常終了：false
	*/
	function setbook($book);
	
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

$dbconn="";

class postgres_i implements db_connector
{
	function db_connect(){
		global $dbconn;
		$dbconn = pg_connect("host=localhost user=postgres dbname=postgres");
		if(!$dbconn){
			exit("DB Connection faild!");
			return false;
		}else{
			return true;
		}
	}
	
	function db_close(){
		global $dbconn;
		pg_close($dbconn);
	}
	
	function setbook($book){
		global $dbconn;
		
		//ここに処理が来た時点で未登録書籍であること
		$query = "INSERT INTO bookshelf(ISBN, URL, Image, Author, PubDate, Title, amount)";
	/* 0 */	$query .= "VALUES ('". $book[0] ."',";	//ISBN(13)
	/* 1 */	$query .= "'". $book[1] ."',";		//Amazon URL
	/* 2 */	$query .= "'". $book[2] ."',";		//写真URL
	/* 3 */	$query .= "'". $book[3] ."',";		//著者
	/* 4 */	$query .= "'". $book[4] ."',";		//出版日
	/* 5 */	$query .= "'". $book[5] ."',";		//タイトル
	/* 6 */	$query .= "1);";			//本の冊数
		
		echo $query;
		pg_query($dbconn, $query);
		return pg_last_error($dbconn);
	}
	
	function addbook($isbn){
		global $dbconn;
		//ここに処理が来た時点で登録済みの書籍であること
		$data = $this->find($isbn);
		//冊数加算
		$data[6] ++;
		
		pg_query($dbconn, "UPDATE bookshelf SET amount={$data[6]} WHERE ISBN='{$isbn}';");
		return pg_last_error($dbconn);
	}
	
	function find($isbn){
		global $dbconn;
		$value = pg_query($dbconn, "SELECT * FROM bookshelf WHERE ISBN='{$isbn}';");
		if(pg_num_rows($value) == 0){	//検索結果NULL？
			return false;
		} else {
			for($i=0; $i<pg_num_rows($value); $i++){
				$data = pg_fetch_row($value, $i);
			}
			return $data;
		}
	}
	
	function rmbook($isbn){
		global $dbconn;
		
		pg_query($dbconn, "DELETE FROM bookshelf WHERE ISBN='{$isbn}';");
		return pg_last_error($dbconn);
	}
	
	function borrow($isbn, $user){
		global $dbconn;
		
		//ここに処理が来た時点で登録済みかつ、貸出中でない書籍であること
		$query = "INSERT INTO borrows(ID, ISBN, bDate, rDate)";
		$query .= "VALUES ('{$user}', '{$isbn}',";
		$query .= "'". date("Y-m-d") ."', NULL);";
		
		pg_query($dbconn, $query);
		return pg_last_error($dbconn);
	}
	
	function repayment($isbn, $user){
		global $dbconn;
		
		$rdate = date("Y-m-d");
		pg_query($dbconn, "UPDATE borrows SET rDate='{$rdate}' WHERE ISBN='{$isbn}' AND ID='{$user}';");
		return pg_last_error($dbconn);
	}
	
	function lending(){
		global $dbconn;
		
		$value = pg_query($dbconn, "SELECT ID, ISBN, bDate FROM borrows WHERE rDate=NULL");
		if(pg_num_rows($value) == 0){	//検索結果NULL？
			return 0;
			exit();
		} else {
			for($i=0; $i<pg_num_rows($value); $i++){
				$data[] = pg_fetch_row($value, $i);
			}
			return $data;
		}
	}
}
?>


