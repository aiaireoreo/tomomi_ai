<?php
  session_start();
  require('dbconnect.php');
  require('functions.php');

  // 仮のログインユーザーデータ
  $_SESSION['id'] = 1;

      // ユーザーがログインしているかどうか
      if (isset($_SESSION['id'])) {
          $id = $_REQUEST['id'];

          // 投稿があるか確認
          $sql = sprintf('SELECT * FROM photos WHERE id=%d', mysqli_real_escape_string($db , $id)
          );

          $record = mysqli_query($db, $sql) or die(mysqli_error($db)
          );

          // 削除したいphotosのデータ一件が格納されている
          $table = mysqli_fetch_assoc($record);

          // 削除したいphotosデータのmember_idと、ログインしているユーザーのidが一致していれば削除処理実行
          if ($table['member_id'] == $_SESSION['id']){
              $sql = sprintf('DELETE FROM phptos WHERE id=%d', mysqli_real_escape_string($db, $id)
          );

              mysqli_query($db, $sql) or die(mysqli_error($db));
          }
      }

  // 削除完了後signin.phpへ遷移
  header('Location: signin.php');
  exit();

?>
