<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事の詳細</title>
</head>
<body>
    <?php
    require('../library.php');
    $db = dbconnect();
    $stmt = $db->prepare('select * from news where id=?');//queryではなくprepareが安全
    if (!$stmt) {//stmtの読み込みがうまくいかなかった場合はエラーを表示
        die($db->error);
    }
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); 
    //数値の代入→newsの詳細ページを呼び出すと指定したnewsを表示することができる
    if (!$id) { //idが正しくない場合にエラーを発生させることができる
        echo '表示する記事を指定してください';
        exit();
    }
    $stmt->bind_param('i', $id);//bind_paramの特性で直接数値をパラメータに指定することができないので変数に代入する
    $stmt->execute(); //実行する

    $stmt->bind_result($id, $news, $created);
    $result = $stmt->fetch();//fetch = DBからデータを一件取り出す
    if (!$result) { //正しく指定はされたもののメモが見つからなかった時のためのエラー処理をしておく
        echo '指定された記事は見つかりませんでした';
        exit();
    }
    ?>

        <div><pre><?php echo htmlspecialchars($news); ?></pre></div> <!--preタグは改行などがそのまま反映される-->

        <p>
            <a href="update.php?id=<?php echo $id; ?>">編集する</a> | 
            <a href="delete.php?id=<?php echo $id; ?>">削除する</a> | 
            <a href="/wine/news">一覧へ</a> |
            <a href="/wine/wine_top2.html">トップページへ</a>
        </p>
</body>
</html>