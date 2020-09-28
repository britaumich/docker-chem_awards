<?php

$abs = __DIR__;
define('BASE_PATH', str_replace('/docs', '', $abs));
define('DEV_CONF', BASE_PATH.'/support/dev_conf.php');
require_once(BASE_PATH.'/support/creds.php');
require_once('noinject.php');
require_once('library/HTMLPurifier.auto.php');
$purifier = new HTMLPurifier();
global $purifier;

date_default_timezone_set('America/Detroit');
$today = date("Y-m-d");
$today_dt = new DateTime($today);
$current_year = academicYear($today_dt);
//echo $current_year;
//$report_year = "2016-2017";
$report_year = "2019";
$committee_email = "brita@umich.edu";
//$committee_email = "chem-awards@umich.edu";
$uploaddir = '/home/appdevch/upload/awards-files/';
global $uploaddir;

//connect to the database
ini_set('display_errors', 'On');
$conn = mysqli_connect($server, $user, $pass) or die("couldn't connect");
mysqli_select_db($conn, $database) or die("couldn't get the db:".mysqli_connect_error());
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$other_admins = array('rsmoke', 'brita');
function is_admin($uniqname)
{
        global $other_admins;
        return array_search($uniqname, $other_admins) !== FALSE;
}
if(file_exists(DEV_CONF)){
    include DEV_CONF;
}
if(isset($dev_user)){
    $uniqname1 = $dev_user;
    $uniqname = $dev_user;
}
elseif(isset($_SERVER['REMOTE_USER'])){
    $uniqname1 = $_SERVER['REMOTE_USER'];
    $uniqname = $_SERVER['REMOTE_USER'];
}
function academicYear(DateTime $userDate) {
    $currentYear = $userDate->format('Y');
    $cutoff = new DateTime($userDate->format('Y') . '/06/31 23:59:59');
    if ($userDate < $cutoff) {
        return ($currentYear-1) . '-' . $currentYear;
    }
    return $currentYear . '-' . ($currentYear+1);
}

/**
 * Sanitize a multidimensional array
 *
 * @uses htmlspecialchars
 *
 * @param (array)
 * @return (array) the sanitized array
 */
function purica_array ($conn, $data = array()) {
        if (!is_array($data) || !count($data)) {
                return array();
        }
        foreach ($data as $k => $v) {
                if (!is_array($v) && !is_object($v)) {
//                      $data[$k] = htmlspecialchars(trim($v));
                        $data[$k] = mysqli_real_escape_string($conn, $v);
                }
                if (is_array($v)) {
                        $data[$k] = purica_array($conn, $v);
                }
        }
        return $data;
}
?>
