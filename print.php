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
		$query .= "<script type='text/javascript'>\n";
		$query .= "<!--\n";
		$query .= "function check(){\n";
		$query .= "    if(window.confirm('本当によろしいですか？')){\n";
		$query .= "        return true;\n";
		$query .= "    }\n";
		$query .= "    else{\n";
		$query .= "        return false;\n";
		$query .= "    }\n";
        $query .= "}\n";
        $query .= "// -->\n";
        $query .= "</script>\n";
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

	function result($data, $st, $br, $fast){
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
		if($br){	//登録済み書籍の場合：貸出フォームの表示
			$query .= "<form action='' method='post'>\n";
			$query .= "<input type='hidden' name='screen' value='bor'>\n";
			$query .= "<input type='hidden' name='bisbn' value='{$data[0]}'>\n";
			$query .= "あなたの識別ID：<input type='text' name='id'>\n";
			$query .= "<input type='submit' value='この本を借りる'>\n";
			$query .= "</form>\n";
		}
		if($br | $st){    //登録済み書籍で貸出できない状態の時
		    $query .= "<hr><form action='' method='post'>\n";
		    $query .= "<input type='hidden' name='screen' value='repay'>\n";
		    $query .= "<input type='hidden' name='isbn' value='{$data[0]}'>\n";
		    $query .= "返却者ID：<input type='text' name='id'>\n";
		    $query .= "<input type='submit' value='返却'></form><hr><br><br>\n";

        	$query .= "<form action='' method='post'>\n";
			$query .= "<input type='hidden' name='screen' value='add'>\n";
			$query .= "<input type='hidden' name='aisbn' value='{$data[0]}'>\n";
			$query .= "<input type='submit' value='この本の冊数を増やす'>\n";
        }
        if(! $st) {	//登録されていない書籍の場合：登録ボタンの表示
            $query .= "<form action='' method='post'>\n";
            $query .= "<input type='hidden' name='screen' value='set'>\n";
            $query .= "<input type='hidden' name='sdata0' value='{$data[0]}'>\n";
            $query .= "<input type='hidden' name='sdata1' value='{$data[1]}'>\n";
            $query .= "<input type='hidden' name='sdata2' value='{$data[2]}'>\n";
            $query .= "<input type='hidden' name='sdata3' value='{$data[3]}'>\n";
            $query .= "<input type='hidden' name='sdata4' value='{$data[4]}'>\n";
            $query .= "<input type='hidden' name='sdata5' value='{$data[5]}'>\n";
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
		$query .= "<input type='button' value='前の画面に戻る' onClick='javascript:window.history.back();'> ";
                $query .= "<input type='button' value='最初の画面に戻る' onClick='location.href(\"http://www.tatsuya-k.net/Book-Management-System/\")'>";


		$this->footer();
	}

	function error($str){
		global $query;

		$this->base();

		$query .= "<h1>＃エラー＃</h1><hr>\n";
		$query .= "<p>". $str ."</p>\n";
		$query .= "<input type='button' value='前の画面に戻る' onClick='javascript:window.history.back();'> ";
		$query .= "<input type='button' value='最初の画面に戻る' onClick='location.href(\"http://www.tatsuya-k.net/Book-Management-System/\")'>";

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

	function adm(){
		global $query;

		//管理画面
		$this->base();

		$query .= "<h1>管理画面</h1>\n";
		$query .= "<p>メニューを選んでください</p>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='hidden' name='screen' value='g_addusr'>\n";
		$query .= "<input type='submit' value='ユーザの追加'>：貸出の際に必要なユーザアカウントを追加します\n";
		$query .= "</form>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='hidden' name='screen' value='g_rmusr'>\n";
		$query .= "<input type='submit' value='ユーザの削除'>：貸出の際に必要なユーザアカウントを削除します\n";
		$query .= "</form>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='hidden' name='screen' value='g_brlist'>\n";
		$query .= "<input type='submit' value='貸出情報照会'>：現在貸出中の書籍一覧を表示します\n";
		$query .= "</form>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='hidden' name='screen' value='g_brhist'>\n";
		$query .= "<input type='submit' value='貸出履歴表示'>：過去に貸し出した本の履歴を表示します\n";
		$query .= "</form>\n";
		$query .= "<form action='' method='post'>\n";
		$query .= "<input type='hidden' name='screen' value='g_rmbook'>\n";
		$query .= "<input type='submit' value=' 本 の 削　除 '>: 本棚に格納されている本を削除します\n";
		$query .= "</form>\n";

		$this->footer();
	}

	function addusr(){
	    global $query;

	    $this->base();

	    $query .= "<h1>貸出ユーザの追加</h1>\n";
	    $query .= "<p>貸出の際に必要なユーザを作成します。以下の項目を入力して送信してください。</p>\n";
	    $query .= "<form action='' method='post'>\n";
	    $query .= "ユーザID：<input type='text' name='uid'><b>必須・５文字以上</b><br>\n";
	    $query .= "（ユーザを特定するときに使います。利用者はこれを記憶しておく必要があります。）\n";
	    $query .= "<br><br>\n";
	    $query .= "あなたの名前：<input type='text' name='uname'><b>必須・５文字以上</b><br>\n";
	    $query .= "（ページ上で表示されるあなたの名前です。何を入力しても構いません。）<br>\n";
	    $query .= "<input type='hidden' name='screen' value='addusr'>\n";
	    $query .= "<input type='submit' value='登録'> <input type='reset' value='リセット'>\n";
	    $query .= "</form>\n";

	    $this->footer();
	}

	function rmusr(){
	    global $query;

	    $this->base();

	    $query .= "<h1>貸出ユーザの削除</h1>\n";
	    $query .= "<p>貸出の際に必要なユーザを削除します。以下の項目を入力して送信してください。</p>\n";
	    $query .= "<form action='' method='post' onSubmit='return check()'>\n";
	    $query .= "ユーザID：<input type='text' name='uid'><br>\n";
	    $query .= "<input type='hidden' name='screen' value='rmusr'>\n";
	    $query .= "<input type='submit' value='削除'> <input type='reset' value='リセット'>\n";
	    $query .= "</form>\n";

	    $this->footer();
	}

	function rmbook(){
	    global $query;

	    $this->base();

	    $query .= "<h1>本の削除</h1>\n";
	    $query .= "<p>本棚に格納されている本のデータを削除します。</p>\n";
	    $query .= "<p>＊注意＊<br>\n";
	    $query .= "・貸出中の書籍は削除出来ません</p>\n";
	    $query .= "<form action='' method='post' onSubmit='return check()'>\n";
	    $query .="<input type='hidden' name='screen' value='rmbook'>\n";
	    $query .="<input type='text' name='isbn'>\n";
	    $query .="<input type='submit' value='削除'>\n";

	    $this->footer();
	}

	function table($title, $header, $data){
	    global $query;

	    $this->base();

	    $query .= "<h1>". $title ."</h1>\n";
	    $query .="<table border='1'>\n";
	    $query .="<tr>\n";
	    for($i=0; $i<count($header); $i++){
	        $query .="<td>". $header[$i] ."\n";
	    }
	    $query .="</tr>\n";
	    for($i=0; $i<count($data); $i++){
	        $query .= "<tr>\n";
	        for($j=0; $j<count($data, COUNT_RECURSIVE)/count($data)-1; $j++){
	            $query .= "<td>". $data[$i][$j] ."</td>\n";
	        }
	        $query .= "</tr>\n";
	    }
	    $this->footer();
	}
}
?>

