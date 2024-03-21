<?php
    $name = '';
    $subject = '';
    $message = '';
    $result = '';
    $posts = null;
    $connectstr_dbhost = getenv("AZURE_SQL_SERVERNAME");
    $connectstr_options = array(
        'Database' => getenv("AZURE_SQL_DATABASE"),
        'UID' => getenv("AZURE_SQL_UID"),
        'PWD' => getenv("AZURE_SQL_PWD")
    );

    // データベースに接続する
    $conn = sqlsrv_connect($connectstr_dbhost, $connectstr_options);
    if (!$conn) {
        echo 'Error: Unable to connect to database.' . PHP_EOL;
        exit;
    }

    if (isset($_POST['name'])) {
        // フォームの入力値を変数に格納する
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // データベースに投稿を登録するSQLを実行する
        $stmt = sqlsrv_prepare($conn, 
            'insert into post(name, subject, message) values (?, ?, ?)',
            array(&$name, &$subject, &$message));
        if (!$stmt || sqlsrv_execute($stmt)) {
            // エラーの場合、エラーメッセージをセットする
            $result = '登録に失敗しました。';
        } else {
            // エラーがない場合、完了メッセージをセットする
            $result = '登録に成功しました。';
        }

        // SQLを解放する
        sqlsrv_free_stmt($stmt);
    }

    // データベースから投稿を取得するSQLを実行する
    $stmt = sqlsrv_prepare($conn, 'select name, subject, message from post');
    if (!$stmt || !sqlsrv_execute($stmt)) {
        // エラーの場合、エラーメッセージをセットする
        $result = '取得に失敗しました。';
    } else {
        // エラーがない場合、結果を取得する
        $posts = array();
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($posts, $row);
        }
        $result = count($posts) . '件取得しました。';
    }

    // データベース接続を終了する
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>問い合わせフォーム</title>
</head>
<body>
    <article>
        <header>
            <h2>問い合わせフォーム</h2>
        </header>
        <section class="center">
            <form action="./contact.php" method="post">
                <p>名前<br><input type="text" name="name" maxlength="30" value="<?php echo($_COOKIE['name']); ?>" readonly></p>
                <p>件名<br><input type="text" name="subject" maxlength="40"></p>
                <p>内容<br><textarea name="message" rows="5" cols="40"></textarea></p>
                <input type="submit" id="send" value="送信">
            </form>
        </section>
        <section>
            <p><?php echo($result); ?><br></p>
            <?php
            if($posts != null) {
                foreach($posts as $row) {
            ?>
                <p>
                    名前：<?php echo($row['name']); ?>&nbsp;&nbsp;件名：<?php echo($row['subject']); ?><br>
                    <?php echo($row['message']); ?>
                </p>
            <?php
                }
            }
            ?>
        </section>
        <footer>
            <p>Copyright&copy; TKproduce All rights reserved.</p>
        </footer>
    </article>
    <script type="text/javascript">
        document.getElementById('send').addEventListener('click', function(event){
            if(!confirm('送信してよろしいですか？')) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
