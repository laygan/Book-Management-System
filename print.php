<?php
/*	
	図書管理システム
	GUI表示スクリプト
*/

$query="";

class draw{
	function base(){
		global $query;
		
		$query  = "<!DOCTYPE html>\n";
		$query .= "<html lang='ja'>\n";
		$query .= "<head>\n";
		$query .= "<meta charset='UTF-8' />\n";
		$query .= "<title>403 Book Management System</title>\n";
		$query .= "<style type='text/css'>\n";
		$query .= ".default{ clear:both; text-align:center; }\n";
		$query .= "p.badges{ clear:both; text-align:right; }\n";
		$query .= "</style>\n";
		$query .= "</head>\n";
		$query .= "<body>\n";
	}
	
	function footer(){
		global $query;
		
		$query .= "</div>\n";
		$query .= "</body>\n";
		$query .= "</html>\n";
		
		echo $query;
	}

	function home(){
		global $query;
		
		//初期画面を出力
		$query  = "<!DOCTYPE html>\n";
		$query .= "<html lang='ja'>\n";
		$query .= "<head>\n";
		$query .= "<meta charset='UTF-8' />\n";
		$query .= "<title>403 Book Management System</title>\n";
		$query .= "<style type='text/css'>\n";
		$query .= ".default{ clear:both; text-align:center; }\n";
		$query .= "p.badges{ clear:both; text-align:right; }\n";
		$query .= "</style>\n";
		$query .= "<script language='JavaScript' type='text/javascript'>\n";
		$query .= "	window.onload = function(){\n";
		$query .= "		document.getElementById('isbn').focus();\n";
		$query .= "	}\n";
		$query .= "</script>\n";
		$query .= "</head>\n";
		$query .= "<body>\n";
		$query .= "<div class='default'>\n";
		$query .= "<h1>403 図書管理システムへようこそ</h1>\n";
		$query .= "<p>まずは本を特定します。<br>ISBNコードを入力し送信してください。</p>\n";
		$query .= "<form id='form' action='controler.php' method='post' target='_self'>\n";
		$query .= "	<input type='hidden' name='screen' value='top'>\n";
		$query .= "	<input type='text' name='isbn' id='isbn' maxlength='15'>\n";
		$query .= "	<input type='submit' value='送信'> <input type='reset' value='リセット'>\n";
		$query .= "</form>\n";
		
		$this->footer();
	}

	function result(){
		global $query;
		
		$this->base();
		
		//検索結果を出力
		$query .= "\n";
		
		$this->footer();
	}
	
	function db_rebuild(){
		global $query;
		
		$this->base();
		
		//データベース新規作成かどうかを尋ねる
		$query .= "<h1>データベースにアクセス出来ませんでした。<br>新しくデータベーステーブルを作成しますか？</h1>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='radio' name='c_db' value='はい'>	<input type='radio' name='c_db' value='いいえ' checked>\n";
		$query .= "<input type='submit' value='決定'>\n";
		$query .= "</form>\n";
		
		$this->footer();
	}
	
	function info($str){
		global $query;
		
		$this->base();
		
		$query .= "<h1>・情報・</h1><hr>\n";
		$query .= "<p>". $str ."</p>\n";
		
		$this->footer();
	}
	
	function error($str){
		global $query;
		
		$this->base();
		
		$query .= "<h1>＃エラー＃</h1><hr>\n";
		$query .= "<p>". $str ."</p>\n";
		
		$this->footer();
	}
}
?>

