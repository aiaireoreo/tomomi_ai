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

    //新規投稿
    if (!empty($_POST)) {
        if ($_POST['title'] == '') {
            $error['title'] = 'blank';
        }

        if ($_POST['comment'] == '') {
            $error['comment'] = 'blank';
        }

        // 対象画像拡張子は.jpgとJEPG
        $fileName = $_FILES['image']['name'];
        if(!empty($fileName)) {
           $ext = substr($fileName, -4);

            if ($ext != '.jpg' && $ext != '.JPG' && $ext != 'jpeg' && $ext != 'JPEG') {
                $error['photo_path'] = 'type';
            }
        }

        // エラーがなければ
        if (empty($error)) {
            $image = date("YmdHis") . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../new_pic/' . $image);

            $sql = sprintf('INSERT INTO photos SET photo_path=%d , title="%s", comment="%s" member_id=%d , created=NOW()',
                $image,
                m($db, $_POST['title']),
                m($db, $_POST['comment']),
                m($db, $member['id']),
                date('Y-m-d H:i:s')
            );
            mysqli_query($db,$sql) or die(mysqli_error($db));


        // ＊signin.phpに遷移
        // header('Location: signin.php');
        // exit();

        }
    }

?>

  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Photo vote</title>

    <link rel="stylesheet" type="text/css" href="./tomomi_aisan2/assets/css/bootstrap.css">
    <!-- ↑bootstrapの読み込み宣言を先にする -->
    <link rel="stylesheet" type="text/css" href="./tomomi_aisan2/assets/css/main.css">

  </head>
  <body>
  <!--
    ===================================================================
    ヘッダー
    -->
  <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="index.php" class="navbar-brand">Photo vote</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="new.php">新規投稿</a></li>
                <li><a href="users/index.php?id=<?php echo h($_SESSION['id']); ?> " >会員情報</a></li>
             </ul>
            <ul class="nav navbar-nav navbar-right">
            <!--   ↑bootstrapでは、右端に寄せるクラス-->
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span id="heder_p_icon"><img src="profile_image/<?php echo h($member['picture_path']); ?>"></span> 
                      <strong><?php echo h($member['nick_name']); ?>さん</strong>
                      <span class="glyphicon glyphicon-chevron-down"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                      <div class="navbar-login">
                        <div class="row">
                          <div class="col-lg-4">
                            <p class="text-center">
                                <span><img class="profile_picture" src="profile_image/<?php echo h($member['picture_path']); ?>"></span>
                            </p>
                          </div>
                          <div class="col-lg-8">
                              <p class="text-left"><strong><?php echo h($member['nick_name']); ?></strong></p>
                              <p class="text-left small"><?php echo h($member['email']); ?></p>
                              <p class="text-left">
                              <a href="users/index.php?=<?php echo h($_SESSION['id']); ?>" class="btn btn-primary btn-block btn-sm">マイプロフィール</a>
                              </p>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li class="divider"></li>
                      <li>
                          <div class="navbar-login navbar-login-session">
                              <div class="row">
                                  <div class="col-lg-12">
                                      <p>
                                          <a href="logout.php" class="btn btn-danger btn-block">ログアウト</a>
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </li>
                  </ul>
              </li>
            </ul>
        </div>
      </div>
    </div>
    <!--
    ===================================================================
    コンテンツ
    -->
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
          <input type="submit" value="投稿完了">
      </div>
    </form>

  <!--
    ===================================================================
    フッター
    -->
    <div class="container">
      <div class="row">
      <hr>
        <div class="col-lg-12">
          <div class="col-md-8">
            <a href="#">Terms of Service</a> | <a href="#">Privacy</a>
          </div>
          <div class="col-md-4">
            <p class="muted pull-right">© 2016 Company Name. All rights reserved</p>
          </div>
        </div>
      </div>
    </div>

    <!-- jsファイルの読み込みはbodyの一番下がデファクトリスタンダード -->
    <!-- jQueryファイルが一番最初 -->
    <script type="text/javascript" src="./assets/js/jquery-1.12.3.min.js"></script>
    <!-- jQueryファイルの次にbootstrapのJSファイル -->
    <script type="text/javascript" src="./assets/js/bootstrap.js"></script>
    <script type="text/javascript" src="./assets/js/main.js"></script>
  </body>
  </html>

