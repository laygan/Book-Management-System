<?php
/*
	PostgreSQL Interface
		interface：５行目〜
		class本文：７０行目〜
*/
interface db_connector
{
	function __construct();
	//コンストラクタはデータベースコネクトのため

	function __destruct();
	//デストラクトはデータベースディスコネクトのため

	/*
	本の情報をデータベースに登録する
	引数：登録するデータの配列
	戻り値：正常完了：true, 異常終了：false
	*/
	function setbook($book);

	/*
	本の冊数をアップデートする
	引数：ISBN, num(数量）
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
	 * 貸出状況のチェック
	 * 引数：isbn, 貸出者ID
	 * 戻り値：引数のisbnで貸し出されている本の冊数
	 */
	function br_check($isbn, $user);

	/*
	本の返却
	引数：isbn,user
	戻り値：正常完了：true, 異常終了：false
	*/
	function repayment($isbn, $user);

	/*
	貸出状況一覧
	引数：無
	戻り値：貸出一覧配列
	*/
	function lending();

	/*
	 * 貸出履歴一覧
	 * 引数：なし
	 * 戻り値：貸出一覧配列
	 */
    function lend_hist();

    /*
     * 貸出用ユーザID検索
     * 引数：ユーザID
     * 戻り値：ユーザ情報配列
     */
    function search_user($uid);

	/*
	 * 貸出用ユーザID追加
	 *引数：ID
	 *戻り値：結果（重複の有無など)
	 */
	function addusr($uid, $uname);

	/*
	 * 貸出用ユーザID削除
	 * 引数：ユーザID
	 * 戻り値：結果
	 */
	function rmusr($uid);
}

$dbconn="";

class postgres_i implements db_connector
{
	function __construct(){
		global $dbconn;
		$dbconn = pg_connect("host=localhost user=postgres dbname=postgres");
		if(!$dbconn){
			exit("DB Connection faild!");
		}
	}

	function __destruct(){
		global $dbconn;
		pg_close($dbconn);
	}

	function setbook($book){
		global $dbconn;
		//ここに処理が来た時点で未登録書籍であること
		$query =  "INSERT INTO bookshelf(ISBN, URL, Image, Author, PubDate, Title, amount)";
	/* 0 */	$query .= "VALUES ('". $book[0] ."',";	//ISBN(13)
	/* 1 */	$query .= "'". $book[1] ."',";		//Amazon URL
	/* 2 */	$query .= "'". $book[2] ."',";		//写真URL
	/* 3 */	$query .= "'". $book[3] ."',";		//著者
	/* 4 */	$query .= "'". $book[4] ."',";		//出版日
	/* 5 */	$query .= "'". $book[5] ."',";		//タイトル
	/* 6 */	$query .= "1);";			//本の冊数

		if(! pg_query($dbconn, $query)){
			echo "ERROR";
		}
		return pg_last_error($dbconn);
	}

	function addbook($isbn, $num){
		global $dbconn;
		//ここに処理が来た時点で登録済みの書籍であること
		pg_query($dbconn, "UPDATE bookshelf SET amount=amount+{$num} WHERE ISBN='{$isbn}';");
		return pg_last_error($dbconn);
	}

	function find($isbn){
		global $dbconn;
		$value = pg_query($dbconn, "SELECT * FROM bookshelf WHERE ISBN='{$isbn}';");
		if(pg_num_rows($value) == 0){	//検索結果NULL？
			return false;
		} else {
			$data;
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

		//同じ人に同じ本を貸出しようとしていないかの判断
		if($this->br_check($isbn, $user) == 0){
		    pg_query($dbconn, "UPDATE bookshelf SET amount=amount-1 WHERE isbn='{$isbn}';");
		    $query  = "INSERT INTO borrows(ID, ISBN, bDate, rDate)";
		    $query .= "VALUES ('{$user}', '{$isbn}', CURRENT_TIMESTAMP, NULL);";
		    pg_query($dbconn, $query);

		    return true;
		}
		else{
		    return false;
		}
	}

	function br_check($isbn, $user){
	    global $dbconn;

	    $data = pg_query($dbconn, "SELECT * FROM borrows WHERE id='{$user}' AND isbn='{$isbn}' AND rdate IS NULL;");
	    $nums = pg_num_rows($data);
        echo $nums;
	    return $nums;
	}

	function repayment($isbn, $user){
		global $dbconn;

		if($this->br_check($isbn, $user) == 1){
		    $stat[0] = pg_query($dbconn, "UPDATE borrows SET rDate=CURRENT_TIMESTAMP WHERE bdate=(SELECT max(bdate) FROM borrows WHERE id='{$user}' AND isbn='{$isbn}');");
		    $stat[1] = pg_query($dbconn, "UPDATE bookshelf SET amount=amount+1 WHERE isbn='{$isbn}';");
		    echo pg_last_error($dbconn);

		    return true;
		}
		else{
		    return false;
		}
	}

	function lending(){
		global $dbconn;

		$value = pg_query($dbconn, "SELECT ID, ISBN, bdate FROM borrows WHERE rdate IS NULL");
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

	function lend_hist(){
	    global $dbconn;

	    $value = pg_query($dbconn, "SELECT * FROM borrows WHERE rdate IS NOT NULL;");
	    if(pg_num_rows($value) == 0){
	        return 0;
	        exit(0);
	    }
	    else{
	        for($i=0; $i<pg_num_rows($value); $i++){
	            $data[] = pg_fetch_row($value, $i);
	        }
	        return $data;
	    }
	}

	function serach_user($uid){
	    global $dbconn;

	    $value = pg_query($dbconn, "SELECT * FROM br_user WHERE id='{$uid}';");
	    $tmp = pg_num_rows($value);
	    for($i=0; $i<$tmp; $i++){
	        $data = pg_fetch_row($value);
	    }
	    if($tmp != 0){
            return $data;
	    }
	    else{
	        return false;
	    }
	}

	function addusr($uid, $uname){
	    global $dbconn;

	    if($this->serach_user($uid) == false){
	        //登録
	        $value = pg_query($dbconn, "INSERT INTO br_user(id, name) VALUES('{$uid}', '{$uname}');");
	        return true;
	    }
	    else{
	        return false;
	    }

	    return pg_last_error($dbconn);
	}

	function rmusr($uid){
	    global $dbconn;

	    $tmp = $this->serach_user($uid);

	    if($tmp == false){
	        return false;
	    }

	    else{
            return pg_query($dbconn, "DELETE FROM br_user WHERE id='{$uid}';");
	    }
	}
}
?>


