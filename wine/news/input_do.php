<?php
//$news = $_POST['news']; => input.html内のニュースの内容を受け取れるけれど、何を書き込まれるかわからないので危険！
//4行目　実際にフォームの値を受け取って$newsという変数の中に代入している
$news = filter_input(INPUT_POST, 'news', FILTER_SANITIZE_SPECIAL_CHARS);//フィルターをかける functionを使う

require('../library.php');
$db = dbconnect();
$stmt = $db->prepare('insert into news(news) values(?)');//ニュースの準備をする
if (!$stmt) { //falseなら終了し、
    die($db->error);
}
$stmt->bind_param('s', $news); //正しければbind_paramでニュースの内容をvalue(?)の箇所に割り当てる
$ret = $stmt->execute(); //$retが正しければ登録して、正しくなければエラーを返す
if ($ret) {
    echo '登録されました';
    echo '<br>→ <a href="index2.php">ニュース一覧へ戻る</a>';
} else {
    echo $db->error;
}
?>