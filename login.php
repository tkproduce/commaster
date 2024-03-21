<?php
    $result = '';
    $connectstr_dbhost = getenv("AZURE_SQL_SERVERNAME");
    $connectstr_options = array(
        'Database' => getenv("AZURE_SQL_DATABASE"),
        'UID' => getenv("AZURE_SQL_UID"),
        'PWD' => getenv("AZURE_SQL_PWD")
    );

    if(isset($_POST['id'])) {
        // フォームの入力値を変数に格納する
        $id = $_POST['id'];
        $pass = $_POST['pass'];

        // データベース接続を開く
        $conn = sqlsrv_connect($connectstr_dbhost, $connectstr_options);
        if (!$conn) {
            echo 'データベース接続に失敗しました。';
            exit;
        }

        // SQLを実行する
        $stmt = sqlsrv_prepare($conn, 
            'select name from customer where id = ? and password = ?',
            array(&$id, &$pass));
        if (!$stmt || !sqlsrv_execute($stmt)) {
            echo 'データの取得に失敗しました。';
            exit;
        }

        // ユーザー名を取得する
        $name = '';
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $name = $row['name'];
        }
        
        if (empty($name)) {
            $result = 'ユーザーIDもしくはパスワードが間違っています。';
        } else {
            // Cookieに値を設定する
            setcookie('name', $name, time() + 60 * 60);
            // メニュー画面を表示する
            header('Location: ./menu.html');
        }

        // データベース接続を解放する
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>ログイン</title>
</head>
<body>
    <article>
        <header>
            <h2>ログイン</h2>
        </header>
        <section class="center">
            <form action="./login.php" method="post">
                <p>ユーザーID：<input type="text" name="id"></p>
                <p>パスワード：<input type="password" name="pass"></p>
                <input type="submit" value="ログイン">
            </form>
            <p class="message"><?php echo(htmlspecialchars($result)) ?></p>
        </section>
        <footer>
            <p>Copyright&copy; TKprodue All rights reserved.</p>
        </footer>
    </article>
</body>
</html>