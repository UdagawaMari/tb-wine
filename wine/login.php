<?php
session_start();
require('library.php');

$error = [];
$email = '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
     $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
     //var_dump($email);
     if ($email === '' || $password === '') {
        $error['login'] = 'blank'; //メールアドレスまたはパスワードのどちらかが空であればエラー
     } else {
        //ログインチェック
        $db = dbconnect();
        $stmt = $db->prepare('select id, name, password from members where email=? limit 1');
        //limit 1　に設定することで、万が一セキュリティを突破されても全部ではなく１件のみの流出で済む仕組み
        if (!$stmt) {
            die($db->error);
        }

        $stmt->bind_param('s', $email);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }

        $stmt->bind_result($id, $name, $hash); //結果を受け取る
        $stmt->fetch();

        //var_dump($hash);ハッシュ化されたパスワードを目視で確認
        //password_verifyでPWが同じかどうかを確認できる
        if(password_verify($password, $hash)) { //$passwordユーザー入力PW、$hash DBより取得したハッシュ化されたPW
        //ログイン成功
        session_regenerate_id(); //session idを生成し直すという処理　長い時間使用するとセッションの情報を盗まれたりする危険性がある
        //セッションの中に保管してDBの負荷を減らす。ログインは成功しているのでセッションに記録する
        $_SESSION['id'] = $id;
        $_SESSION['name'] = $name;
        header('Location: /wine/wine_top2.html');
        exit();
        } else {
            $error['login'] = 'failed';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <title>代理店様ログイン</title>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ログインする</h1>
    </div>
    <div id="content">
        <div id="lead">
            <p>メールアドレスとパスワードを記入してログインしてください。</p>
            <p>登録手続きがまだの代理店様はこちらからどうぞ。</p>
            <p>&raquo;<a href="join/">登録手続きをする</a></p>
        </div>
        <form action="" method="post">
            <dl>
                <dt>メールアドレス</dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($email); ?>"/>
                    <?php if (isset($error['login']) && $error['login'] === 'blank'): ?>
                    <p class="error">* メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if(isset($error['login']) && $error['login'] === 'failed'): ?>
                    <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="255" value="<?php echo h($password) ?>" />
                </dd>
            </dl>
            <div>
                <input type="submit" value="ログインする"/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
