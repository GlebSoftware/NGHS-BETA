<?php require_once('../Connections/nghsbeta.php'); ?>
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
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../login";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>

<html>
  <head>
    <title>NG Beta Picture Day Pass</title>
    <meta name="viewport" content="width=device-width" />    
  </head>
<body style="font-family:Arial, Helvetica, sans-serif; font-size:14px; text-align:center; webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; cursor: default;">

<img src="../img/logo.png" />
<p><strong>Beta Club Picture Day Pass</strong></p>
<?php if ($row_memberInformation['role']=="member") { // Member menu?>
<p><strong><?php echo $row_memberInformation['fName'] . " " . $row_memberInformation['lName']; ?></strong> should be dismissed from class at <strong>7:30 AM </strong>on <strong>Thursday, January 23rd </strong>for the <strong>Beta Club </strong>yearbook picture. These pictures will be in the <strong> gym. </strong>Please wear your club t-shirt!</p>
<?php }
else if ($row_memberInformation['role']=="officer" || $row_memberInformation['role']=="team") { // Officer and Leaderhip Team menu?>
<p><strong><?php echo $row_memberInformation['fName'] . " " . $row_memberInformation['lName']; ?></strong> should be dismissed from class at <strong>7:30 AM </strong>on <strong>Thursday, January 23rd </strong>for the <strong>Beta Club </strong>yearbook picture. These pictures will be in the <strong> gym. </strong>Please wear your Beta t-shirt!</p>
<?php } ?>
<p><em>Megan Johnston, Miranda Simmons, and Taylor Holcombe</em></p>
</body>
</html>
