<?php

    session_start();
    require('dbconnect.php');
    require('functions.php');

    //ログイン
    if (isset($_SESSION['id'])) {
        $id = $_REQUEST['id'];

        $sql = sprintf('SELECT * FROM members WHERE id=%d',
            m($db, $_SESSION['id'])
        );

        $record = mysqli_query($db, $sql) or die (mysqli_error($db));
        $table = mysqli_fetch_assoc($record);

        if ($table['id'] == $_SESSION['id']){
            $sql = sprintf('DELETE FROM photos WHERE id=%d',
            m($db, $id)
            );
            mysqli_query($db, $sql) or die(mysqli_error($db));
        }
    }

    header('Location:index.php');
    exit();

?>
