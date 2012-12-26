<?php
	include_once("amazon.php");
	include_once(dirname(__FILE__)."/db_interface/postgres_i.php");
	include_once("print.php");
	
	if(!isset($_POST["screen"])){
		$pr = new draw();
		$pr->home();
	}
	
	else if($_POST["screen"] == "top"){
		$pr = new draw();
		
		//データベース接続
		$db = new postgres_i();
		
		//データベース接続失敗時
		if(! $db->db_connect()){
			$pr->error("データベースに接続できませんでした。");
			exit(1);
		} else {
			$data = $db->find($_POST["isbn"]);
		}
		
		if(!$data){
			//Amazon APIを使用してISBN適合情報を検索する
			$am = new amazon();
			$xml = $am->search($_POST["isbn"]);
		} else {
			$pr->info("OK");
		}
	}
	
	else{
		
	}
?>
