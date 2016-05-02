<?php

    session_start();
    require('dbconnect.php');
    require('functions.php');

    //ログイン
    $_SESSION['time'] = time();
    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
        $sql = sprintf('SELECT * FROM members WHERE id=%d',
            m($db, $_SESSION['id'])
        );

        $record = mysqli_query($db, $sql) or die (mysqli_error($db));
        $table = mysqli_fetch_assoc($record);

        // 削除対象のphotoデータidとログインidが一致すれば削除処理実行
        if ($table['id'] == $_SESSION['id']){
            $sql = sprintf('DELETE FROM photos WHERE id=%d',
            m($db, $_SESSION['id'])
            );
            mysqli_query($db, $sql) or die(mysqli_error($db));
        }

    $team_members = array('1' => 'hoge','2' => 'hogeho','3' => 'hogehoge');
    var_dump($team_members);

    }

    header('Location:index.php');
    exit();

?>
