<?php
  session_start();
  require('dbconnect.php');
  require('functions.php');

  // 仮のログインユーザーデータ
  $_SESSION['id'] = 1;
  $_SESSION['time'] = time();

      // ログイン判定
      if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time() ) {
          $_SESSION['time'] = time();

          $sql = sprintf('SELECT * FROM members WHERE id=%d',
              m($db, $_SESSION['id'])
          );
          $record = mysqli_query($db, $sql) or die(mysqli_error($db));

          // ログインしている時のユーザーのデータ
          $member = mysqli_fetch_assoc($record);
      } else {
          // ログインしていない
          header('Location: signin.php');
          exit();
        }


  // ＊新規投稿セット
  $_FILES['image']['name'] = 'phpto_path';
  $fileName = $_FILES['image']['name'];

      // 写真を登録
      if (empty($error)) {
      $image = date("YmdHis") . $_FILES['image']['name'];

      // ＊イメージをサーバーにアップロード
      move_uploaded_file($_FILES['image']['name'], '../member_picture/' . $image);


      // Q：入力内容確認ボタンを押した際に確認用(check.php)、完了用(complete.php)ページは不要？
      //(seed_snsのjoinの様なページ)
      // $_SESSION['join'] = $_POST;
      // $_SESSION['join']['image'] = $image;

      // check.phpに遷移
      // header('Location: check.php');
      // exit();
      }

      // 投稿を記録
      if (!empty($_POST)) {
          if ($_POST['title'] != '') {
              $sql = sprintf('INSERT INTO photos SET photo_path=%d , title="%s", comment="%s" member_id=%d , created=NOW()',
              m($db, $_FILES['image']),
              m($db, $_POST['title']),
              m($db, $_POST['comment']),
              m($db, $member['member_id'])
              );
              mysqli_query($db,$sql) or die(mysqli_error($db));
          }
       }


      //＊バリデーションセット
      // Q: バリデーションエラーがあった際にエラーを蓄えるための配列を用意
      // $error = array();

      // エラー確認1: titleが空だったら$error配列のtitleキーにblankという値を代入
      if (!empty($_POST)) {
          if ($_POST['title'] == '') {
              $error['title'] = 'blank';
          }

          // エラー確認2: commentが空だったら$error配列のcommentキーにblankという値を代入
          if ($_POST['comment'] == '') {
              $error['comment'] = 'blank';
          }

          // エラー確認3: upされたものが画像ファイルか確認、拡張子がjpgもしくはpngでなければtypeというエラーを表示
          if(!empty($fileName)) {
              $ext = substr($fileName, -3);
              echo $ext;

              if ($ext != 'jpg' && $ext != 'png') {
                  $error['image'] = 'type';
              }
          }
      }


      // Q：重複投稿チェックは必要か？
      // (重複してるかを調べる為に必要な基準となるものは何？photo_path？投稿時間が名前が変わってしまうから無理？)
      // ここにいれてるコードはもし入れるとしたら・・・の仮定文
      // if (empty($error)) {
      //     $sql = sprintf('SELECT COUNT(*) AS cnt FROM photos WHERE photo_path="%d"',
      //     m($db, $_FILES['photo_path']),
      //     );
      //     $record = mysqli_query($db,$sql) or die(mysqli_error($db));
      //     $table = mysqli_fetch_assoc($record);

      //     if ($table['cnt'] > 0) {
      //         $error['photo_path'] = 'duplicate';
      //     }
      // }


      // ＊signin.phpに遷移
      // header('Location: signin.php');
      // exit();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Photo vote</title>

  <link rel="stylesheet" type="text/css" href="../tomomi.aisan/assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../tomomi.aisan/assets/css/new.css">

</head>
<body>
  <h2>新規写真投稿画面</h2>
  <form action="" method="post" enctype="multipart/form-data">

    <d1>
      <dt>写真</dt>
      <dd>
        <input type="file" name="image">
          <?php if(!empty($error['image'])): ?>
          <?php if($error['image'] == 'type'): ?>
            <p class="error">画像を登録してください。</p>
          <?php endif; ?>
          <?php endif; ?>
      </dd>

      <br>

      <dt>タイトル</dt>
      <dd>
        <?php if(!empty($_POST['title'])): ?>
          <input type="text" name="title" value="<?php echo h($_POST['title'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
          <input type="text" name="title" value="">
        <?php endif; ?>

        <!-- phpでエラー内容出力 -->
        <?php if(!empty($error['title'])): ?>
          <?php if ($error['title'] == 'blank'): ?>
            <p class="error">タイトルを入力してください。</p>
          <?php endif; ?>
        <?php endif; ?>
      </dd>

      <br>

      <dt>コメント</dt>
      <dd>
        <?php if(!empty($_POST['comment'])): ?>
          <textarea name="comment" cols="40" rows="4"><?php echo h($_POST['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <?php else: ?>
          <textarea name="comment" cols="40" rows="4"></textarea>
        <?php endif; ?>

        <!-- phpでエラー内容出力 -->
        <?php if(!empty($error['comment'])): ?>
          <?php if ($error['comment'] == 'blank'): ?>
            <p class="error">コメントを入力してください。</p>
          <?php endif; ?>
        <?php endif; ?>
      </dd>
    </d1>

    <br>

    <div>
        <input type="submit" value="入力内容確認">
    </div>
  </form>
</body>
</html>
