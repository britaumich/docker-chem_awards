<!DOCTYPE html>
<html>
<head>
<style>
.descrbox {
        width: 90%;
        background: #f9f9f9;
margin: auto;
font-size:150%;
padding-top: 5px%;
}

.title {
        padding: 0px;
        margin: 0px;
        font-family: Garamond, sans-serif;
        font-size:  26px;
        color: #071b2a;
        font-weight: normal;
}
</style>
</head>
<?php


$name=check_input($_REQUEST['name']);
$text=check_input($_REQUEST['text']);
echo '<br><div class="title">';
echo $name;
echo '</div><br><div class="descrbox">';
echo $text;


function check_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


?>

