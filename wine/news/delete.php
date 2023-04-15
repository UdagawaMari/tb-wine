<?php
//require('dbconnect.php');
require('../library.php');
$db = dbconnect();
$stmt = $db->prepare('delete from news where id=?');
if (!$stmt) {
    die($db->error);
}
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    echo '記事が正しく指定されていません';
    exit();
}
$stmt->bind_param('i', $id);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}

header('Location: index.php');
?>