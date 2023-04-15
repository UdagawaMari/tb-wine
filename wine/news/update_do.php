<?php
require('../library.php');
$db = dbconnect();

$stmt = $db->prepare('update news set news=? where id=?'); //今回は２つパラメータを指定している
if (!$stmt) {
    die($db->error);
}
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$news = filter_input(INPUT_POST, 'news', FILTER_SANITIZE_STRING);
$stmt->bind_param('si', $news, $id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}

header('Location: news.php?id=' . $id);//うまくいったらnews.phpへ移動する。idをURLパラメータとして渡すというプログラム
?>