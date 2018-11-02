
<?php
// データベース接続
try {

  $dsn = 'データベース名';
  $user = 'ユーザー名';
  $password = 'パスワード';
  $pdo = new PDO($dsn,$user,$password,
  array(
    PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    )
  );
  // echo "接続しました。";

  // テーブル作成
  $sql = "CREATE TABLE IF NOT EXISTS db04 (id INT NOT NULL auto_increment PRIMARY KEY,
                                           name char(32) NOT NULL,
                                           comment TEXT NOT NULL,
                                           password char(12) NOT NULL);";
  $stmt = $pdo->query($sql);
  // echo "テーブルを作成しました。";

  // テーブル一覧の確認
  // $sql = "SHOW TABLES";
  // $result = $pdo->query($sql);
  // foreach ($result as $row) {
  //   echo $row[0];
  //   echo "<br>";
  // }
  // echo "<hr>";

  // 変数定義
  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $password = $_POST['password'];
  $edit_id = $_POST['edit'];
  $edit_mode_id = $_POST['edit_mode'];
  $delete_id = $_POST['delete'];


  if (empty($edit_mode_id)) {
  // 投稿新規作成
    if (!empty($name) && !empty($comment) && !empty($password)) {
      $sql = $pdo->prepare("INSERT INTO db04 (name, comment, password) VALUES (:name, :comment, :password)");
      $sql->bindParam(':name', $name);
      $sql->bindParam(':comment', $comment);
      $sql->bindParam(':password', $password);
      $sql->execute();
    } elseif (!empty($name) || !empty($comment)) {
      $error_message = "入力してください！";
    }
  } else {
  // 編集投稿処理
    if (!empty($name) && !empty($comment) && !empty($password)) {
      $sql = $pdo->prepare("update db04 set name=:name, comment=:comment, password=:password where id=$edit_mode_id");
      $sql->bindParam(':name', $name);
      $sql->bindParam(':comment', $comment);
      $sql->bindParam(':password', $password);
      $sql->execute();
    } elseif (!empty($name) || !empty($comment)) {
      $error_message = "入力してください！";
    }
  }

  // 編集処理(テキストファイルからデータをフォームに格納)

  if (!empty($edit_id)) {
    $sql = "select * from db04 where id=$edit_id";
    $datas = $pdo->query($sql);
    $datas = $datas->fetch();
    if ($password == $datas['password']) {
      $data_id = $datas['id'];
      $data_name = $datas['name'];
      $data_comment = $datas['comment'];
      $data_password = $datas['password'];
    } else {
      $error_message = "入力してください！";
    }
  }

  // 削除処理
  if (!empty($delete_id)) {
    $sql = "select * from db04 where id=$delete_id";
    $datas = $pdo->query($sql);
    $datas = $datas->fetch();
    if ($password == $datas['password']) {
      $sql = "delete from db04 where id=$delete_id";
      $result = $pdo->query($sql);
    } else {
      $error_message = "入力してください！";
    }
  }

  //データ抽出
  $sql = "SELECT * FROM db04";
  $results = $pdo->query($sql);


} catch (PDOException $e) {
  $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>mission4-1</title>
</head>
<body>
  <div class="container">
    <?php
      if (!empty($error_message)) {
        echo $error_message;
      }
    ?>
    <!-- 投稿フォーム -->
    <form action="mission_4-1.php" method="post">
      <input type="text" name="name" placeholder="名前" value="<?php echo $data_name; ?>"><br>
      <input type="text" name="comment" placeholder="コメント" value="<?php echo $data_comment; ?>"><br>
      <input type="text" name="password" placeholder="パスワード" value="<?php echo $data_password; ?>"><br>
      <input type="hidden" name="edit_mode" value="<?php echo $data_id; ?>">
      <input type="submit">
    </form>

    <!-- 編集フォーム -->
    <form action="mission_4-1.php" method="post">
      <input type="text" name="edit" placeholder="編集対象番号"><br>
      <input type="text" name="password" placeholder="パスワード"><br>
      <input type="submit" value="編集">
    </form>

    <!-- 削除フォーム -->
    <form action="mission_4-1.php" method="post">
      <input type="text" name="delete" placeholder="削除対象番号"><br>
      <input type="text" name="password" placeholder="パスワード"><br>
      <input type="submit" value="削除">
    </form>

    <?php
      // 投稿一覧表示
      foreach ($results as $row) {
        echo $row['id'] . ' ';
        echo $row['name'] . ' ';
        echo $row['comment'] . "<br>";
      }
    ?>
  </div>
</body>
</html>
