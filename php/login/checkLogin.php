<?php

include ('../../dll/config.php');
include('../login/isLogin.php');

extract($_POST);

if (isset($_SESSION["IDROLKBUS" . $site_city])) {
    if ((int)$idRolKBus !== $_SESSION["IDROLKBUS" . $site_city]) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "1";
}

