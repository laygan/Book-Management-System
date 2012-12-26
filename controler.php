<?php
	include_once("amazon.php");
	include_once(dirname(__FILE__)."/db_interface/postgres_i.php");
	include_once("print.php");
	
	if(isset($_GET["mode"])){
		$pr = new draw();
		$pr->s_add();
		exit(0);
	}
	
	else if(!isset($_POST["screen"])){
		$pr = new draw();
		$pr->home();
	}
	
	else if($_POST["screen"] == "top"){
		if($_POST["isbn"] == "fast"){
			$pr = new draw();
			$pr->s_add();
			exit(0);
		}
		setter(false);
	}
	
	else if($_POST["screen"] == "add"){
		$pr = new draw();
		$db = new postgres_i();
		
		$db->db_connect();
		$db->setbook();
	}
	
	else if($_POST["screen"] == "sop"){
		setter(true);
	}
	
	else{
		
	}

	function isbn_checker($value){
		$tmp = strlen($value);
		if($tmp==10 || $tmp==13){
			//ISBN変換（１０から１３へ）
			if ($tmp == 10) {
				//ISBN10からISBN13への変換
				$ISBNtmp = "978" . $value;
				$sum = 0;
				for ($i = 0; $i < 12; $i++) {
					$weight = ($i % 2 == 0 ? 1 : 3);
					$sum += (int)substr($ISBNtmp, $i, 1) * (int)$weight;
				}
				//チェックディジットの計算
				$checkDgt = (10 - $sum % 10) == 10 ? 0 : (10 - $sum % 10);
				return  "978" . substr($value, 0, 9) . $checkDgt;
			} else {
				return $value;
			}
		} else {
			$pr = new draw();
			$pr->error("不正な値が入力されました。");
			exit(0);
		}
	}
	
	function setter($sp){
		//ISBN check
		$isbn = isbn_checker($_POST["isbn"]);
		
		$pr = new draw();
		$db = new postgres_i();
		
		//データベース接続
		if(! $db->db_connect()){
			//データベース接続失敗時
			$pr->error("データベースに接続できませんでした。");
			exit(1);
		} else {
			//データベースから情報を検索
			$data = $db->find($isbn);
		}
		
		//データベースから情報を得たか
		if(!$data){
			echo "Powered by Amazon.co.jp";
			//情報取得に失敗、Amazon APIを使用してISBN適合情報を検索
			$am = new amazon();
			$data = $am->search($isbn);
			$data[0] = isbn_checker($data[0]);
			if(! $db->setbook($data)){
				if($sp){
					$pr->result($data, false, true);
				} else {
					$pr->result($data, false, false);
				}
			} else {
				$pr->error("データベース手続きが失敗しました。");
			}
		} else {
			if($sp){
				$pr->info("この本は登録済みです。");
			} else {
				//戻り値を表示へ
				$pr->result($data, true);
			}
		}
		$db->db_close();
	}

?>
