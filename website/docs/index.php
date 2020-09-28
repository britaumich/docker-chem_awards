<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Chemistry Awards System - University of Michigan</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META content="" name=KEYWORDS>
<META content="" name=description>
<link rel="stylesheet" href="eebstyle.css">
</head>

<body>
<div class="bodypad">
<div align="center"><br>
<div class="facrecbox1"><div class="textalignleft pad15and10">
<div align="center"><br><br><h1>Chemistry Awards<br></h1><br>
<bR><div align="center"><img src="images/linecalendarpopup500.jpg"></div><Br>
<?php
//echo ($_SERVER["DOCUMENT_ROOT"]);
//require_once($_SERVER["DOCUMENT_ROOT"] . '../support/awards_dbConnect.inc');
include('awards-config.php');


//$uniqname1 = $_SERVER['REMOTE_USER'];

// $uniqname1 = $_SERVER['REDIRECT_REMOTE_USER'];

if (is_admin($uniqname1)) {
?>
<form action="admin/allawards.php">
    <input type="submit" value="Admin Site" />
</form>
<?php
}
?>
<form action="faculty/index.php">
    <input type="submit" value="Faculty Site" />
</form>
</div>
<bR><div align="center"><img src="images/linecalendarpopup500.jpg"></div><Br>

</body>
</html>

