<?php
function renderForm($id, $name, $error)
 {
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>
 <head>
<title>Chemistry Awards System - University of Michigan</title>
<link rel="stylesheet" href="../eebstyle.css">
 </head>
<body>
<div class="bodypad">
<div align="center"><br>
<div class="facrecbox1"><div class="textalignleft pad15and10">
<div align="center"><br><br><h1>Faculty Awards<br></h1><br>
</div>
<?php
 if ($error != '')
 {
 echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
 }

 ?>
<div align="center">
<table>
 <form action="" method="post">
 <input type="hidden" name="id" value="<?php echo $id; ?>"/>
<tr><th> ID: 
<td><?php echo $id; ?></tr>
<tr><th>Name: 
<td> <input type="text" name="name" value="<?php echo $name; ?>"/></tr>
</table>
 <input type="submit" name="submit" value="Submit">
 </div>
<div align='center'><img src='../images/linecalendarpopup500.jpg'></div><br>

 </form>
 </body>
 </html>
 <?php
 }  // function
ob_start();
require_once('../awards-config.php');
require_once('nav.php');

if (isset($_REQUEST['submit'])) {
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
   $id = $purifier->purify($_REQUEST['id']);
   $name = $purifier->purify($_REQUEST['name']);
   if ($name == '') {
       // generate error message
       $error = 'ERROR: name is empty!';
    // if either field is blank, display the form again
      renderForm($id,  $name,  $error);
   }  // empty
   else
   {
       // save the data to the database
      mysqli_query($conn, "UPDATE eligibility_list SET name='$name' WHERE id='$id'") or die(mysqli_error($conn));
             // once saved, redirect back to the view page
      header("Location: edit_eligibility.php");
ob_end_flush();
   }

 } //    isset $id
 else
 {
 // if the 'id' isn't valid, display an error
 echo 'Error!';
 }


}  // isset($_POST['submit']
else {
// if the form hasn't been submitted, get the data from the db and display the form
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0) {
      // query db
   $id = $purifier->purify($_REQUEST['id']);
   $result = mysqli_query($conn, "SELECT * FROM eligibility_list WHERE id=$id") or die(mysqli_error($conn));
   $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    if($row) {
         // get data from db
          $name = $row['name'];
       // show form
        renderForm($id, $name, $error);

    }   // if $row
    else   {
       // if no match, display result
      echo "No results!";
    }
  }   // isse $id
  else  {
     // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
    echo 'Error!';
   }
 }

?>
