<?php

require("emoji.php");
require("Mobile_detect.php");
/* if ($_SERVER['REMOTE_ADDR'] !== '5.157.115.83')
  Header("Location: /work.html"); */

$_BASEPATH = '';
$_GLOBAL_ARRAY_TRUE = array("result" => true);
$_GLOBAL_ARRAY_FALSE = array("result" => false);
$_MAJOCLOCK_INSTAGRAM_ID = "##REMOVED";
$_MAJOCLOCK_INSTAGRAM_SECRET = "##REMOVED";
$_MAJOCLOCK_INSTAGRAM_ACCESSTOKEN = "##REMOVED";

function DB_CONNECT() {
    $DB_NAME = "##REMOVED";
    $DB_HOST = "##REMOVED";
    $DB_USER = "##REMOVED";
    $DB_PASS = "##REMOVED";
    return $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
}

function REPLACE_ALPHANUM($arg) {
    return preg_replace("/[^A-Za-z0-9_!]/", '', $arg);
}

function REPLACE_ALPHANUM_SPACE($arg) {
    return preg_replace("/[^A-Za-z0-9_!\s]/", '', $arg);
}

function REPLACE_NUM($arg) {
    return preg_replace("/[^0-9]/", '', $arg);
}

function NEED_ADMINISTRATOR_PRIVILEGES() {
    if ($_SESSION['priv'] != 1) {
        echo '<div class="alert alert-danger"><h2>Errore</h2>Non hai i privilegi per accedere a questa pagina.<br>(Error: #PRIV.ADMIN.BASE.0)</div>';
        exit;
    }
}

function NEED_EVENT_PRIVILEGES() {
    if ($_SESSION['priv'] <= 3) {
        echo '<div class="alert alert-danger">Non hai i privilegi per visionare questa pagina.(PRIV.EVENT.BASE.0)</div>';
        exit;
    }
}

function NEED_DJ_PRIVILEGES() {
    if ($_SESSION['priv'] <= 4) {
        echo '<div class="alert alert-danger">Non hai i privilegi per visionare questa pagina.(PRIV.EVENT.BASE.0)</div>';
        exit;
    }
}

function NEED_NOTOBE_MARRO() {
    if ($_SESSION['sede'] == 'MARRO') {
        echo '<div class="alert alert-danger">Non hai i privilegi per visionare questa pagina.(PRIV.EVENT.SECT.M)</div>';
        exit;
    }
}

function BOOL_ISMARRO() {
    $a = false;
    if (isset($_SESSION['sede']) && ($_SESSION['sede'] == 'MARRO')) {
        $a = true;
    }
    return $a;
}

function BOOL_EVENT_PRIVILEGES() {
    $a = false;
    if (isset($_SESSION['priv']) && ($_SESSION['priv'] <= 3)) {
        $a = true;
    }
    return $a;
}

function BOOL_DJ_PRIVILEGES() {
    $a = false;
    if (isset($_SESSION['priv']) && ($_SESSION['priv'] <= 4)) {
        $a = true;
    }
    return $a;
}

function BOOL_EVENT_SECURITY() {
    $a = false;
    if (isset($_SESSION['priv']) && ($_SESSION['priv'] == 3)) {
        $a = true;
    }
    return $a;
}

function BOOL_ADMINISTRATOR_PRIVILEGES() {
    $a = false;
    if ($_SESSION['priv'] == 1) {
        $a = true;
    }
    return $a;
}

function Rand_String() {
    $int = rand(0, 51);
    $a_z = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $rand_letter = $a_z[$int];
    return $rand_letter . time();
}

function random() {

    $chars = "ABCDEFGHILMNOPQRSTUVZXYK023456789";
    srand((double) microtime() * 1000000);
    $i = 0;
    $pass = '';

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}

function REPLACE_DESCRIPTION($arg) {
    return preg_replace("/[^A-Za-z0-9\s.!,';]/", '', $arg);
}

function NEED_LOGIN() {
    if (!isset($_SESSION['logged'])) {
        header("Location: login.php");
    }
}

function VALID_PARAM($param) {
    if (isset($param) && $param != '' && !empty($param))
        return true;
    else
        return false;
}

function GET_USER_AVATAR($id) {
    $mysqli = DB_CONNECT();
    $id = REPLACE_ALPHANUM($id);
    $query = $mysqli->query("SELECT * FROM MAJOUS_users WHERE id='$id' LIMIT 1;");
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        return $row['avatar'];
    }
}

function DataITA($dataTime) {
    $data = date("j/m/Y", $dataTime);
    $ora = date("H:i", $dataTime);
    $ieri = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
    $oggi = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
    if ($data == $ieri)
        $dataOk = "Ieri alle";
    elseif ($data == $oggi)
        $dataOk = "Oggi alle";
    else
        $dataOk = $data;
    return("$dataOk $ora");
}

function validateEMAIL($EMAIL) {
    $v = "/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/";

    return (bool) preg_match($v, $EMAIL);
}

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

session_name('MAJOUS_SID');
session_start();

function MAJO_process($data) {
    $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
    $replace = array('>', '<', '\\1');
    $data = emoji_docomo_to_unified($data);   # DoCoMo devices
    $data = emoji_kddi_to_unified($data);     # KDDI & Au devices
    $data = emoji_softbank_to_unified($data); # Softbank & pre-iOS6 Apple devices
    $data = emoji_google_to_unified($data);   # Google Android devices
    $data = preg_replace($search, $replace, $data);
    $data = "$data \n \n<!--This source is minified and processed for emoji optimization - mrboo -->\n<!--Creator Gabriele Dragotto mrboo.eu -->";
    return $data;
}

function get_fb_likes($url) {
    $query = "select total_count,like_count,comment_count,share_count,click_count from link_stat where url='{$url}'";
    $call = "https://api.facebook.com/method/fql.query?query=" . rawurlencode($query) . "&format=json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $call);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output);
    /*
     * $fb_likes = reset( get_fb_likes("http://www.cnn.com") );
      echo $fb_likes->total_count;
      echo $fb_likes->like_count;
      echo $fb_likes->comment_count;
      echo $fb_likes->share_count;
      echo $fb_likes->click_count;
     */
}

function shorten_string($string, $wordsreturned) {
    $retval = $string;  //  Just in case of a problem
    $array = explode(" ", $string);
    /*  Already short enough, return the whole thing */
    if (count($array) <= $wordsreturned) {
        $retval = $string;
    }
    /*  Need to chop of some words */ else {
        array_splice($array, $wordsreturned);
        $retval = implode(" ", $array) . " ...";
    }
    return $retval;
}

function GetIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//ob_start("MAJO_process");
?>
