<?php

/* htmlspecialcharsを短くする */
function h($value) {//何度も出てくる時自分でファンクション作成して短くする
    return htmlspecialchars($value, ENT_QUOTES);//htmlspecialcharsに第２引数でent_quotesを与えて特殊文字を変換
    //ENT_QUOTESはPHPが定数としてもっているint型の値であり、ENT_QUOTESを指定すると、特殊文字のうちシングルクォーテーションとダブルクォーテーションも変換対象に含めるようになります。
}

//DB接続を共通化
function dbconnect() {
$db = new mysqli('mysql57.blaublau23.sakura.ne.jp', 'blaublau23', 'momo130614', 'blaublau23_mywine');//データベースに出力
	if (!$db) {
		die($db->error);//もしうまく接続できなければエラーを表示
	}

    return $db;
}
?>