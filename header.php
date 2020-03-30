<?php require_once('Connections/nghsbeta.php'); ?>
<?php

//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
$colname_memberInformation = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_memberInformation = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_memberInformation = sprintf("SELECT * FROM beta_members WHERE stuID = %s", GetSQLValueString($colname_memberInformation, "int"));
$memberInformation = mysql_query($query_memberInformation, $nghsbeta) or die(mysql_error());
$row_memberInformation = mysql_fetch_assoc($memberInformation);
$totalRows_memberInformation = mysql_num_rows($memberInformation);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
    <meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
    <meta name="viewport" content="width=device-width" />
    <title><? echo $pageTitle; ?> | North Gwinnett Beta Club</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script src="js/twitter.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css'>
    <? echo $moreHead; ?>
  </head>
<body>
<?php include_once("analyticstracking.php") ?>
<?php if ($noMenu==false) { ?>
<?php if ($row_memberInformation['role']=="member") { // Member menu?>
<div class="header">
	<div class="inner">
    	<a href="index"><h1><img src="img/logo.png" class="logo" /><span>NG</span> Beta</h1></a>
      <nav>
        	<a href="news">News</a>
            <a href="events">Volunteer</a>
            <a href="about">About</a>
            <a href="contact">Contact</a>
            <a href="dashboard" style="background: #F03204;padding: 5px 10px;color: #FFF;"><?php echo $row_memberInformation['fName']; ?></a>
      </nav>
  </div>
</div>
<div class="newsBar" id="latestTweet"></div>
<?php }
else if ($row_memberInformation['role']=="officer" || $row_memberInformation['role']=="team") { // Officer and Leaderhip Team menu?>
<div class="header">
	<div class="inner">
    	<a href="index"><h1><img src="img/logo.png" class="logo" /><span>NG</span> Beta</h1></a>
      <nav>
        	<a href="admin.php">Admin</a>
        	<a href="news">News</a>
            <a href="events">Volunteer</a>
            <a href="about">About</a>
            <a href="contact">Contact</a>
            <a href="dashboard" style="background: #F03204;padding: 5px 10px;color: #FFF;"><?php echo $row_memberInformation['fName']; ?></a>
      </nav>
  </div>
</div>
<div class="newsBar" id="latestTweet"></div>
<?php }
else { //Regular menu?>
<div class="header">
	<div class="inner">
    	<a href="index"><h1><img src="img/logo.png" class="logo" /><span>NG</span> Beta</h1></a>
      <nav>
        	<a href="news">News</a>
            <a href="events">Events</a>
            <a href="about">About</a>
            <a href="contact">Contact</a>
            <a href="social">Social</a>
            <a href="login" style="background: #F03204;padding: 5px 10px;color: #FFF;">Login</a>
      </nav>	
  </div>
</div>
<div class="newsBar" id="latestTweet"></div>
<?php } ?>
<?php } ?>


<?php include('imageSelector.php') ?>
