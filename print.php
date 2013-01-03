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
		$query .= ".main{ width:100%; }\n";
		$query .= ".main div{ width:80%; margin:0 auto;}\n";
		$query .= "p.badges{ clear:both; text-align:right; }\n";
		$query .= "img.col{ float:left; }\n";
		$query .= "</style>\n";
		$query .= "</head>\n";
		$query .= "<body>\n";
		$query .= "<div class='main'><div>\n";
	}
	
	function footer(){
		global $query;
		
		$query .= "</div></div>\n";
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
		$query .= ".main{ width:100%; }\n";
		$query .= ".main div{ width:80%; margin:0 auto;}\n";
		$query .= "p.badges{ clear:both; text-align:right; }\n";
		$query .= "img.col{ float:left; }\n";
		$query .= "</style>\n";
		$query .= "<script language='JavaScript' type='text/javascript'>\n";
		$query .= "	window.onload = function(){\n";
		$query .= "		document.getElementById('isbn').focus();\n";
		$query .= "	}\n";
		$query .= "</script>\n";
		$query .= "</head>\n";
		$query .= "<body>\n";
		$query .= "<div class='main'><div>\n";
		$query .= "<h1>403 図書管理システムへようこそ</h1>\n";
		$query .= "<p>まずは本を特定します。<br>ISBNコードを入力し送信してください。</p>\n";
		$query .= "<form id='form' action='controler.php' method='post' target='_self'>\n";
		$query .= "	<input type='hidden' name='screen' value='top'>\n";
		$query .= "	<input type='text' name='isbn' id='isbn' maxlength='15'>\n";
		$query .= "	<input type='submit' value='送信'> <input type='reset' value='リセット'>\n";
		$query .= "</form>\n";
		
		$this->footer();
	}

	function result($data, $sw, $fast){
		global $query;
        
		if($fast){  //連続追加時の自動転送metaタグ付加
			$query  = "<!DOCTYPE html>\n";
			$query .= "<html lang='ja'>\n";
			$query .= "<head>\n";
			$query .= "<meta charset='UTF-8' />\n";
			$query .= "<title>403 Book Management System</title>\n";
			$query .= "<style type='text/css'>\n";
			$query .= ".main{ width:100%; }\n";
			$query .= ".main div{ width:80%; margin:0 auto;}\n";
			$query .= "p.badges{ clear:both; text-align:right; }\n";
			$query .= "img.col{ float:left; }\n";
			$query .= "</style>\n";
			$query .= "<meta http-equiv='refresh' content='1 ; URL=controler.php?mode=f'>\n";
			$query .= "</head>\n";
			$query .= "<body>\n";
			$query .= "<div class='default'>\n";
				
		} else {
			$this->base();
		}
		//検索結果を出力
		$query .= "<h1>検索結果</h1>\n";
		$query .= "<a href='". $data[1] ."' target='_blank'>";
		$query .= "<h2>". $data[5] ."</h2></a>\n";
		$query .= "<a href='". $data[1] ."' target='_blank'>";
		$query .= "<img src='". $data[2] ."' class='col'></a>\n";
		$query .= "<h3>ISBN：". $data[0]. "<br>\n";
		$query .= "著者：". $data[3]. "<br>\n";
		$query .= "出版日：". $data[4]. "</h3>\n";
		
        
        $query .= "<form action='' method='post'>\n";
		if($sw){    　//登録済み書籍の場合：冊数追加ボタンの表示
			$query .= "<input type='hidden' name='screen' value='add'>\n";
			$query .= "<input type='hidden' name='aisbn' value='{$data[0]}'>\n";
			$query .= "<input type='submit' value='この本の冊数を増やす'>\n";
        } else {      //登録されていない書籍の場合：登録ボタンの表示
            $query .= "<input type='hidden' name='screen' value='set'>\n";
            $query .= "<input type='hidden' name='sdata[0]' value='{$data[0]}'>\n";
            $query .= "<input type='hidden' name='sdata[1]' value='{$data[1]}'>\n";
            $query .= "<input type='hidden' name='sdata[2]' value='{$data[2]}'>\n";
            $query .= "<input type='hidden' name='sdata[3]' value='{$data[3]}'>\n";
            $query .= "<input type='hidden' name='sdata[4]' value='{$data[4]}'>\n";
            $query .= "<input type='hidden' name='sdata[5]' value='{$data[5]}'>\n";
            $query .= "<input type='submit' value='この本を本棚に入れる'>\n";
        }
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
	
	function s_add(){
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
		$query .= "<h1>連続登録</h1>\n";
		$query .= "<p>まずは本を特定します。<br>ISBNコードを入力し送信してください。</p>\n";
		$query .= "<form id='form' action='controler.php' method='post' target='_self'>\n";
		$query .= "	<input type='hidden' name='screen' value='sop'>\n";
		$query .= "	<input type='text' name='isbn' id='isbn' maxlength='15'>\n";
		$query .= "	<input type='submit' value='送信'> <input type='reset' value='リセット'>\n";
		$query .= "</form>\n";
		
		$this->footer();
	}
}
?>

