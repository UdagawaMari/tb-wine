<?php
require('../library.php');
$db = dbconnect();

/*最大ページ数を求める　floor((件数+1) / 5 + 1)*/ 
$counts = $db->query('select count(*) as cnt from news');
$count = $counts->fetch_assoc();
$max_page = floor(($count['cnt']+1)/5+1);//ceil使用して、５にプラス１しないやり方もある
//echo $max_page;

$stmt = $db->prepare('select * from news order by id desc limit ?, 5');//SQLで抽出。order by 並べ替え、id順に、desc大きい順 ＝　新しいnewsを上から順に並べる
if (!$stmt) {//newsが正しく取得できなければエラーを表示
    die($db->error);
}
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
//１０行目のif文を三項演算子で書くと、
$page = ($page ?: 1);
// if (!$page) { //$pageが省略された時に最初から表示する。そうでないと何も表示されなくなる
//     $page = 1;
// }
$start = ($page - 1) * 5;//zeroスタートなので。そしてページごとに５件ずつ取得
$stmt->bind_param('i', $start);
$result = $stmt->execute();//入力されたページの値が例：９９９などとなると画面に表示されないのでresult=と定義して３０行目にif文入れる
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ニュース一覧</title>
</head>
<body>
    <h1>ニュース一覧</h1>
    <!--<p>→ <a href="input.html">新しい記事を投稿する</a></p>-->

    <?php if (!$result): ?>
        <p>表示する記事はありません</p>
    <?php endif; ?>
    <?php $stmt->bind_result($id, $news, $created); ?>
    <?php while ($stmt->fetch()): ?> <!--fetch呼び出す-->
        <div>
            <!--23行目　mb_substr(パラメータ３つ)１つ目は対象の文字、２つ目が返し位置、３つ目が何文字切り取るか、という指定-->
            <h2><a href="news.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars(mb_substr($news, 0, 50)); ?></a></h2> <!--['news']はカラム名、$newsは$newsから受け取ったデータ、そしてnewsの内容が崩れていかないようにhtmlspecialcharsファンクションをかける-->
            <time><?php echo htmlspecialchars($created); ?></time><!--カラム名はcreated-->
        </div>
        <hr>
    <?php endwhile; ?>

    <p>
        <?php if ($page > 1): ?><!-- 0ページ目への案内は不要なのでif文で制御する -->
            <a href="?page=<?php echo $page-1; ?>"><?php echo $page-1; ?>ページ目へ</a> | 
        <?php endif; ?>
        <?php if ($page < $max_page): ?>
            <a href="?page=<?php echo $page+1; ?>"><?php echo $page+1; ?>ページ目へ</a> |
        <?php endif; ?>
        <a href="/wine/wine_top.html">トップページへ</a>
    </p>
</body>
</html>