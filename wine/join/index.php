<?php //入力なしで次へ進もうとするとエラーが出るように作る
session_start();
require('../library.php');

//書き直すをクリックしたとき記入したものが全て白紙にならないようにする
if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',//初期化する
        'email' => '',
        'password' => '',
    ];
}
$error = [];


/*フォームの内容をチェック、送信された時だけ動かす　　*/ 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);//sanitize=文字列の中の有害な文字を検知し無害化すること。filter_input=フォームのユーザーが入力したものを$form['name']に代入
    if ($form['name'] === '') { //フォームの値が空ならば
    $error['name'] = 'blank';//blank何も入ってないというエラーを記録して、後で使用する
    }

    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error['email'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('select count(*) from members where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $form['email']);
        $success = $stmt->execute();
        if(!$success) {
            die($db->error);
        }

        $stmt->bind_result($cnt);
        $stmt->fetch();
        //var_dump($cnt)

        if ($cnt > 0) {
            $error['email'] = 'duplicate';
        }
    }

    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    } else if (strlen($form['password']) < 4) {//strlen=文字列の長さ
        $error['password'] = 'length';
    }
        //画像のチェック
        $image = $_FILES['image'];
        if ($image['name'] !== '' && $image['error'] === 0) { //nameという項目が空でないか、かつエラーが起こっていなければという条件
            $type = mime_content_type($image['tmp_name']); //type変数に指定したファイルpng,jpegなどかどうか判断　mime_content_typeで今何のファイルがアップロードされたか見ることができる
            if ($type !== 'image/png' && $type !== 'image/jpeg') { //pngでもjpegでもないファイルはエラーとして処理する
                $error['image'] = 'type';
            }
        }

        if (empty($error)) { //何も値が入っていないということは空ではないということ
            $_SESSION['form'] = $form; //＄formという値を入れると一気に全部が拾える

            //画像のアップロード
            if ($image['name'] !== '') {
               $filename = date('YmdHis') . '_' . $image['name'];//ファイル名の重複を避けるため日付を入れる
                if (!move_uploaded_file($image['tmp_name'], '../member_picture/' . $filename)) {
                //move_uploaded_fileファンクションは一時的な場所から正式な場所へ移動する。一つ目のパラメータは一時保管場所、二つ目は実際の場所
                    die('ファイルのアップロードに失敗しました');
                } 
                $_SESSION['form']['image'] = $filename;
            } else {
                $_SESSION['form']['image'] = '';
            }

            header('Location: check.php');
            exit();
        }

}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>代理店会員登録</title>

    <link rel="stylesheet" href="../css/style.css"/>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>代理店会員登録</h1>
    </div>

    <div id="content">
        <p>次のフォームに必要事項をご記入ください。</p><!--次の行form action=""とからのままにしているのは画面上でエラーが出るように自分に返ってくるため-->
        <form action="" method="post" enctype="multipart/form-data"><!--multipart/formdata写真のアップロードがある時使う-->
            <dl>
                <dt>会社名<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="name" size="60" maxlength="255" value="<?php echo h($form['name']); ?>"/><!--value属性に-->
                    <?php if (isset($error['name']) && $error['name'] === 'blank'): ?><!--$error['name']が空だったら下記の文章を表示-->
                        <!-- isset関数：引数に指定した変数に値が設定されている、かつ、NULLではない場合にはtrue(正)の値を戻り値とします。 それ以外は、戻り値にfalse(偽)の値を返します。-->
                        <p class="error">* 会社名を入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>メールアドレス<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($form['email']); ?>"/>
                    <?php if (isset($error['email']) && $error['email'] === 'blank'): ?>
                    <p class="error">* メールアドレスを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['email']) && $error['email'] === 'duplicate'): ?> <!--エラーのemailがduplicateであったらエラーメッセージを表示-->
                    <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                    <?php endif; ?>
                <dt>パスワード<span class="required">必須</span></dt>
                <dd>
                    <input type="password" name="password" size="10" maxlength="20" value="<?php echo h($form['password']); ?>"/>
                    <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
                    <p class="error">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['password']) && $error['password'] === 'length'): ?>
                    <p class="error">* パスワードは4文字以上で入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>写真など</dt>
                <dd>
                    <input type="file" name="image" size="35" value="test"/>
                    <?php if (isset($error['image']) && $error['image'] === 'type'): ?>
                    <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
                    <?php endif; ?>
                    <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                </dd>
            </dl>
            <div><input type="submit" value="入力内容を確認する"/></div>
        </form>
    </div>
</body>

</html>