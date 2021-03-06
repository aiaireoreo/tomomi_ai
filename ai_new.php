<?php

    session_start();
    require('dbconnect.php');
    require('functions.php');

    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {

        //ログイン
        $_SESSION['time'] = time();
        $sql = sprintf('SELECT * FROM members WHERE id=%d',
          m($db, $_SESSION['id'])
        );

        $record = mysqli_query($db, $sql) or die (mysqli_error($db));

        // ログインしているユーザーのデータ
        $member = mysqli_fetch_assoc($record);
        } else {
        //ログインしてない
        header('Location; login.php');
        exit();
    }


    $error = array();

    if(!empty($_POST)){

        if ($_POST['title'] == ''){
          $error['title'] = 'blank';
        }

        if ($_POST['comment'] == ''){
          $error['comment'] = 'blank';
        }

    
        // 写真のエラー文
        $fileName = $_FILES['photo_path']['name'];



        if (empty($fileName)) {
            $error['photo_path'] = 'blank';
        }


        if (!empty($fileName)){

          $ext = substr($fileName, -4);

          if ($ext != '.jpg' && $ext != '.JPG' && $ext != 'jpeg' && $ext != 'JPEG'){
            $error['photo_path'] = 'type';
          }

        }


        if(empty($error)){

            $image = date('YmdHis') . $_FILES['photo_path']['name'];
            echo $image;

            move_uploaded_file($_FILES['photo_path']['tmp_name'],'./vote_photo/' . $image);

            
            $sql = sprintf('INSERT INTO photos SET photo_path="%s", 
              title="%s", comment="%s", member_id="%d", created=NOW()',
              $image,
              m($db, $_POST['title']),
              m($db, $_POST['comment']),
              m($db, $member['id']),
              date('Y-m-d H:i:s')
            );

            mysqli_query($db, $sql) or die(mysqli_error($db));
            unset($_SESSION['join']);
            header('Location: index.php');
            exit();
        }
    }
    

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Photovote</title>
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
          <li><a href="users/index.php?id=<?php echo h($member['id']); ?>" >会員一覧</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
<!--  ↑bootstrapでは、右端に寄せるクラス--> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span id="heder_p_icon"><img src="profile_image/<?php echo h($member['picture_path']) ?>"></span> 
              <strong><?php echo h($member['nick_name']); ?></strong>
              <span class="glyphicon glyphicon-chevron-down"></span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <div class="navbar-login">
                <div class="row">
                  <div class="col-lg-4">
                    <p class="text-center">
                        <span class="glyphicon glyphicon-user icon-size"></span>
                    </p>
                  </div>
                  <div class="col-lg-8">
                    <p class="text-left"><strong>nick_name</strong></p>
                    <p class="text-left small">email</p>
                    <p class="text-left">
                      <a href="#" class="btn btn-primary btn-block btn-sm">マイプロフィール</a>
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
                      <a href="#" class="btn btn-danger btn-block">ログアウト</a>
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
      <div class="panel-heading"><strong>新規投稿</strong>
        <small>&nbsp;&nbsp;あなたのベストショットを投稿しよう！</small></div>


        <form action="" method="post" enctype="multipart/form-data">

          <div class="panel-body">
            <h5>写真をアップロードする<span class="required">*</span></h5>

            <input type="file" name="photo_path">

            <?php if(!empty($error['photo_path'])): ?>

              <?php if($error['photo_path'] == 'blank'): ?>
                <p class="error">写真の選択は必須です。</p>
              <?php endif; ?>

              <?php if($error['photo_path'] == 'type'): ?>
                <p class="error">jpgで指定してください。</p>
              <?php endif; ?>

            <?php endif; ?>
            <br>
          

              <div class="control-group">
              <h5>タイトルを入力してください。<span class="required">*</span></h5>
            <div class="controls">   
              <?php if(!empty($_POST['title'])): ?>
            <input type="text" name="title" value="<?php echo h($_POST['title']); ?>">
            <?php else: ?>
              <input type="text" name="title" value="">
            <?php endif; ?>
            <!-- phpでエラー内容出力 -->
            <?php if(!empty($error['title'])): ?>
              <?php if($error['title'] == 'blank'): ?>
                <p class="error">タイトルを入力してください。</p>
              <?php endif; ?>
            <?php endif; ?>
                              </div>
          </div>

        <div class="control-group">
          <h5>写真に関するエピソードを書いてみんなにアピールしよう！<span class="required">*</span></h5>
          <div class="controls">  
          <?php if(!empty($_POST['comment'])): ?>
            <input type="text" class="photo_message" name="comment" value="<?php echo h($_POST['comment']); ?>">
          <?php else: ?>
            <input type="text" class="photo_message" name="comment" placeholder="全角1500文字以内で記入してください。">
          <?php endif; ?>

          <?php if(!empty($error['comment'])): ?>
            <?php if($error['comment'] == 'blank'): ?>
              <p class="error">コメントを入力してください。</p>
            <?php endif; ?>
          <?php endif; ?>
                   
          </div>
        </div>
        <input type="submit" class="btn btn-sm btn-primary" id="js-upload-submit" value="投稿する">
        </form>
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