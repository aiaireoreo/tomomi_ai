<?php
    session_start();

    // 外部ファイルの読み込み
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

        // ログインしているのユーザーのデータ
        $member = mysqli_fetch_assoc($record);
    } else {
        header('Location: signin.php');
        exit();
    }

    
        $id = $_REQUEST['id'];
        $sql = 'SELECT * FROM photos WHERE id='.$id;
        $result = mysqli_query($db, $sql) or die (mysqli_error($db));
        $photo = mysqli_fetch_assoc($result);



    $error = array();

    if (!empty($_POST)) {

        //エラー項目の確認
        if ($_POST['comment'] == '') {
          $error['comment'] = 'blank';
        }
     

      // 編集後のコメントを登録
    if (isset($_POST['comment']) && empty($error['comment'])) {
      $new_comment = $_POST['comment'];

      $sql = sprintf('UPDATE photos SET comment="%s" WHERE id=%d',
              m($db, $new_comment),
              m($db, $id)
              );
      mysqli_query($db, $sql) or die(mysqli_error($db));
      header('Location: index.php');
      exit();
      }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>投稿編集</title>
  <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css">
  <!-- ↑bootstrapの読み込み宣言を先にする -->
  <link rel="stylesheet" type="text/css" href="./assets/css/main.css"> 
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
          <li><a href="users/index.php" >会員一覧</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
<!--  ↑bootstrapでは、右端に寄せるクラス--> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <span id="heder_p_icon"><img src="profile_image/<?php echo h($member['picture_path']); ?>"></span> 
              <strong><?php echo h($member['nick_name']) ?></strong>
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
                    <p class="text-left"><strong><?php echo h($member['nick_name']) ?></strong></p>
                    <p class="text-left small"><?php echo h($member['email']) ?></p>
                    <p class="text-left">
                      <a href="users/index.php" class="btn btn-primary btn-block btn-sm">マイプロフィール</a>
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
  <div class="container">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>『<?php echo h($photo['title']) ?>』のエピソードを編集しますか？</strong>
      </div>
        <div class="panel-body">
           <!-- Standar Form -->
          <h5>投稿されている写真</h5>
            <img class="photo_show" src="vote_photo/<?php echo h($photo['photo_path']) ; ?>">
            <br><br>
          
          <!-- Textarea -->
          <div class="control-group">
            <h5>エピソードの編集ができます。<span class="required">*</span></h5>
            <form action="" method="post" enctype="multipart/form-data">
              <?php if(!empty($_POST['comment'])): ?>
                <textarea type="text" name="comment" class="photo_message"><?php echo h($photo['comment']); ?></textarea>
              <?php else: ?>
                <textarea type="text" name="comment" class="photo_message"><?php echo h($photo['comment']); ?></textarea>
              <?php endif; ?>
              <?php if(!empty($error['comment'])): ?>
              <?php if($error['comment'] == 'blank'): ?>
                <p><span class="required">※エピソードの入力は必須です。</span></p>
              <?php endif; ?>
              <?php endif; ?>

              <input type="submit" class="btn btn-sm btn-primary" value="編集完了">  
            </form>
            </div>
          </div>
      </div>
    </div>
  </div> 
  <!-- /container -->
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

