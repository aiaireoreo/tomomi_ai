<?php
    // 全体で頻繁に使用する関数

    // htmlspecialcharsのショートカット
    function h($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    // mysqli_real_escape_stringのショートカット
    function m($db, $value) {
        return mysqli_real_escape_string($db, $value);
    }
?>
