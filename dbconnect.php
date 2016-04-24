<?php
    // データベース接続処理

  $db = mysqli_connect('localhost', 'root', 'mysql', 'photo_vote')
  or die(mysqli_connect_error());

  mysqli_set_charset($db, 'utf8');

?>
