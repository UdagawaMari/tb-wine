<?php
session_start();
require('../library.php');

$form = $_SESSION['form'];//sessionで入力画面と確認画面を行き来させる

//セッションの表示がなければcheck.phpを表示させない
if (isset($_SESSION['form'])) { //こちらが存在すればセッションの中にこの値を入れる
	$form = $_SESSION['form'];
} else { //そうでなければ、直接呼び出されてしまった場合はindex.phpに移動させる
	header('Location: index.php'); 
	exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$db = dbconnect();
	$stmt = $db->prepare('insert into members (name, email, password, image) VALUES (?, ?, ?, ?)');
	if (!$stmt) {
		die($db->error);
	}
	$password = password_hash($form['password'], PASSWORD_DEFAULT);
	$stmt->bind_param('ssss', $form['name'], $form['email'], $password, $form['image']);
	$success = $stmt->execute();
	if (!$success) {
		die($db->error);
	}

	unset($_SESSION['form']); //セッションの内容を消してから完了画面へ移動する
	header('Location: thanks.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>代理店会員登録</title>

	<link rel="stylesheet" href="../css/style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>代理店会員登録</h1>
		</div>

		<div id="content">
			<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
			<form action="" method="post">
				<dl>
					<dt>会社名</dt>
					<dd><?php echo h($form['name']); ?></dd>
					<dt>メールアドレス</dt>
					<dd><?php echo h($form['email']); ?></dd>
					<dt>パスワード</dt>
					<dd>
						【表示されません】
					</dd>
					<dt>写真など</dt>
					<dd>
							<img src="../member_picture/<?php echo h($form['image']); ?>" width="100" alt="" />
					</dd>
				</dl>
				<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
			</form>
		</div>

	</div>
</body>

</html>