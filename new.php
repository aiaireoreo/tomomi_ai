<?php
  session_start();
  require('dbconnect.php');
  // require('functions.php');

  // 仮のログインユーザーデータ
  $_SESSION['id'] = 1;
  $_SESSION['time'] = time();

      // ログイン判定
      if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time() ) {
          $_SESSION['time'] = time();

          $sql = sprintf('SELECT * FROM members WHERE id=%d',
              mysqli_real_escape_string($db, $_SESSION['id'])
          );
          $record = mysqli_query($db, $sql) or die(mysqli_error($db));

          // ログインしているのユーザーのデータ
          $member = mysqli_fetch_assoc($record);
      } else {
          // ログインしていない
          header('Location: signin.php');
          exit();
      }

            // 新規投稿セット
            // 写真の登録
            $_FILES['image']['name'] = 'phpto_path';
            $fileName = $_FILES['image']['name'];

                // もしエラーがなければ
                if (empty($error)) {
                $image = date("YmdHis") . $_FILES['image']['name'];
                // move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
                }

                  // 投稿した際の確認画面が必要か？(seed_snsのcheck.phpとthanks.phpページ的なもの)
                  // $_SESSION['join'] = $_POST;
                  // $_SESSION['join']['image'] = $image;


                        // 投稿を記録
                        if (!empty($_POST)) {
                            if ($_POST['title'] != '') {
                                $sql = sprintf('INSERT INTO photos SET photo_path=%d , title="%s", comment="%s" member_id=%d , created=NOW()',
                                mysqli_real_escape_string($db, $_FILES['photo_path']),
                                mysqli_real_escape_string($db, $_POST['title']),
                                mysqli_real_escape_string($db, $_POST['comment']),
                                mysqli_real_escape_string($db, $member['member_id'])
                                );

                                mysqli_query($db,$sql) or die(mysqli_error($db));
                            }
                         }

             // 写真の有無チェック

              // 写真がない場合、「写真が選択されていません」と表示
              // 写真のエラー文

              // if(!empty($fileName)) {
              // $ext = substr($fileName, -3);
              // substr()関数は指定した文字列から指定した数だけ文字を取得する
              // 今回は-3と指定することで文字列の最後から３つ取得
              // echo $ext;

              // 画像ファイルの拡張子がjpgもしくはpngでなければtypeというエラーを出す
              // if ($ext != 'jpg' && $ext != 'png') {
              // $error['image'] = 'type';
              // }
              // }

              // イメージをサーバーにアップロード


              // signin.phpに遷移
              // header('Location: signin.php');
              // exit();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Photo vote</title>
</head>
  <body>
    <h2>新規写真投稿画面</h2>

      <div>
        <dt>写真</dt>
        <dd>
          <input type="file" name="image">
            <?php if(!empty($error['image'])): ?>
            <?php if($error['image'] == 'type'): ?>
              <p class="error">画像を登録してください。</p>
            <?php endif; ?>
            <?php endif; ?>

        </dd>
      </div>
      <br>

      <div>
        <dt>タイトル</dt>
        <dd>
          <textarea name="commenta" cols="40" rows="2"></textarea>
        </dd>
      </div>

        <br>

        <div>
        <dt>コメント</dt>
          <dd>
            <textarea name="commenta" cols="60" rows="5"></textarea>
          </dd>
        </div>

        <br><br>

        <div>
          <dd>
            <input type="submit" value="入力内容確認">
          </dd>
        </div>

      </form>
  </body>
</html>
