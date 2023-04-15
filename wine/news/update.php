<?php
require('../library.php');
$db = dbconnect();
$stmt = $db->prepare('select * from news where id=?');
if (!$stmt) {
    die($db->error);
}
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt->bind_param('i', $id);
$stmt->execute();

$stmt->bind_result($id, $news, $created);
$result = $stmt->fetch();
if (!$result) {
    die('記事の指定が正しくありません');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事の編集</title>
</head>
<body>
    <form action="update_do.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>"> <!--hidden 画面上に表示されないインプット構文を作る-->
        <textarea name="news" cols="50" rows="10" placeholder="記事を投稿してください"> <?php echo htmlspecialchars($news); ?></textarea><br>
        <button type="submit">編集する</button>
    </form>
</body>
</html>