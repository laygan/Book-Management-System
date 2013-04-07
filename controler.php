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
		else if($_POST["isbn"] == "admin"){
			$pr = new draw();
			$pr->adm();
			exit(0);
		}
		setter(false);
	}

	else if($_POST["screen"] == "sop"){
		setter(true);
	}

	else if($_POST["screen"] == "add"){
		$pr = new draw();
		$db = new postgres_i();
		if(! $db->addbook($_POST["aisbn"])){
			$pr->info("冊数の追加が完了しました。");
		} else {
			$pr->error("冊数を追加できませんでした。");
		}
	}

	else if($_POST["screen"] == "set"){
		$pr = new draw();
		$db = new postgres_i();

		for($i=0; $i<6; $i++){  //formデータを配列に格納する
			$data[] = $_POST["sdata".$i];
		}

		$retu = $db->setbook($data);
		if(! $retu){
		    $pr->info("本棚にしまいました。");
		} else {
			$pr->error("データベースに登録できませんでした。<br>".$retu);
		}
	}

	//貸出処理
	else if($_POST["screen"] == "bor"){
		$pr = new draw();
		$db = new postgres_i();

        if($_POST["id"] == ""){
            $pr->error("IDを入力してください");
        }

        else{
            $user = $db->serach_user($_POST["id"]);
            if(! $user){
                $pr->error("そのユーザは存在しません");
            }
            else{
                if(! $db->borrow($_POST["bisbn"], $_POST["id"])){
                    $pr->error("貸出処理に失敗しました");
                    exit(0);
                }
                $pr->info($user[1] ."で貸出処理が完了しました");
            }
        }
	}

	//返却処理
	else if($_POST["screen"] == "repay"){
	    $pr = new draw();
	    $db = new postgres_i();

	    if(! $db->repayment($_POST["isbn"], $_POST["id"])){
	        $pr->error("返却処理に失敗しました");
	    }
	    else{
	        $pr->info("返却処理が完了しました");
	    }

	}

	//ユーザ追加画面
	else if($_POST["screen"] == "g_addusr"){
	    $pr = new draw();
	    $pr->addusr();
	}

	//ユーザ追加処理
	else if($_POST["screen"] == "addusr"){
	    $pr = new draw();
	    $db = new postgres_i();

	    if(mb_strlen($_POST["uid"]) <=4 || mb_strlen($_POST["uname"]) <= 4){
	        $pr->error("登録に失敗しました。");
	        exit(0);
	    }
	    if($db->addusr($_POST["uid"], $_POST["uname"])){
	        $pr->info("登録が完了しました。");
	    }
	    else{
	        $pr->error("登録に失敗しました。");
	    }
	}

    //ユーザ削除画面
	else if($_POST["screen"] == "g_rmusr"){
	    $pr = new draw();
	    $pr->rmusr();
	}

	//ユーザ削除処理
	else if($_POST["screen"] == "rmusr"){
	    $pr = new draw();
	    $db = new postgres_i();

	    $tmp = $db->serach_user($_POST["uid"]);

	    if($tmp == false){
	        $pr->error("ユーザが存在しません");
	    }
	    else{
	        echo $db->rmusr($_POST["uid"]);
            $pr->info("ユーザを削除しました");
	    }
	}

	//貸出リスト出力
	else if($_POST["screen"] == "g_brlist"){
	    $pr = new draw();
	    $db = new postgres_i();

	    $data = $db->lending();
	    if($data != 0){
	        $head = array("利用者ID", "書籍ISBN", "貸出日");
	        $pr->table("貸出中書籍一覧", $head, $data);
	    }
	    else{
	        $pr->info("貸出中書籍はありません。");
	    }
	}

	//貸出履歴出力
	else if($_POST["screen"] == "g_brhist"){
	    $pr = new draw();
	    $db = new postgres_i();

	    $data = $db->lend_hist();
	    if($data != 0){
	        $head = array("利用者ID", "書籍ISBN", "貸出日", "返却日");
	        $pr->table("貸出履歴一覧", $head, $data);
	    }
	    else{
	        $pr->info("貸出履歴はありません。（貸出中書籍はあるかもしれません）");
	    }
	}

	//本の削除前画面
	else if($_POST["screen"] == "g_rmbook"){
	    $pr = new draw();
	    $pr->rmbook();
	}

	//本の削除処理
	else if($_POST["screen"] == "rmbook"){
	    $pr = new draw();
	    $db = new postgres_i();

	    $lend = $db->br_check($_POST["isbn"], "%");
	    if($lend == 0){
	        $db->rmbook($_POST["isbn"]);
	        $pr->info("削除処理が完了しました");
	    }
	    else{
	        $pr->error("削除処理が失敗しました");
	    }
	}
	else{}

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

		//データベースから情報を検索
		$data = $db->find($isbn);


		//データベースから情報を得たか
		if(!$data){
			echo "Powered by Amazon.co.jp";
			//情報取得に失敗、Amazon APIを使用してISBN適合情報を検索
			$am = new amazon();
			$data = $am->search($isbn);
			$data[0] = isbn_checker($data[0]);

            if($sp){
                $retu = $db->setbook($data);
                if(! $retu){
                    //本棚に格納されていない、貸出不可、連続登録
                    $pr->result($data, false, false, true);
                }
                else{
                    $pr->error("登録に失敗しました");
                }
			} else {
			    //本棚に格納されていない、貸出不可、手動登録
				$pr->result($data, false, false, false);
			}

		} else {
		    //本棚にあった本
			if($sp){
			    //連続登録時には冊数を増やす
				$db->addbook($isbn);
				$pr->info("この本は登録済みのため、本の冊数を追加しました。");
			} else {
				if($data[6] <= 0){
				    //本棚に格納されていた（在庫なし状態）、貸出不可、手動登録
				    $pr->result($data, true, false, false);
				}
				else{
				    //本棚に格納されている、貸出可、手動登録
				    $pr->result($data, true, true, false);
				}
			}
		}
	}

?>
