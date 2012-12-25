<?php
/*	
	図書管理システム
	GUI表示スクリプト
*/

class draw{
	function home(){
		//初期画面を出力
		$query  = "<!DOCTYPE html>\n";
		$query .= "<html lang="ja">\n";
		$query .= "<head>\n";
		$query .= "<meta charset='UTF-8' />\n";
		$query .= "<title>403 Book Management System</title>\n";
		$query .= "<style type='text/css'>\n";
		$query .= "p.default{ text-align:center; }\n";
		$query .= "p.badges{ text-align:right; }\n";
		$query .= "</style>\n";
		$query .= "</head>\n";
		$query .= "<body>\n";
		$query .= "</body>\n";
		$query .= "</html>\n";
		
		echo $query;
	}
	
	function result(){
		//検索結果を出力
		
	}
?>
