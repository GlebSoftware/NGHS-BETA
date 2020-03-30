<?php require_once('../Connections/nghsbeta.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "officer";
$MM_donotCheckaccess = "false";

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
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
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
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addEvent")) {
  $insertSQL = sprintf("INSERT INTO events (eName, eDate, eTime, eLocation, eDescription, eOfficer, eLink, eShifts, eRequirement, eQuestion, eActive) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['Date'], "date"),
                       GetSQLValueString($_POST['Time'], "text"),
                       GetSQLValueString($_POST['Location'], "text"),
                       GetSQLValueString($_POST['Description'], "text"),
                       GetSQLValueString($_POST['OfficerStudentID'], "text"),
                       GetSQLValueString($_POST['Link'], "text"),
                       GetSQLValueString($_POST['Shifts'], "text"),
                       GetSQLValueString($_POST['Requirement'], "text"),
                       GetSQLValueString($_POST['OtherQuestion'], "text"),
                       GetSQLValueString(isset($_POST['Active']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
<meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<script src="js/twitter.js"></script>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
<title>North Gwinnett Beta Club</title>
</head>

<body>
<div class="section gray">
  <h1>Add An Event</h1>
  <form action="<?php echo $editFormAction; ?>" method="POST" name="addEvent" target="_self">
  Name: <input name="Name" type="text" maxlength="50" /><br />
  Date: <input name="Date" type="text" value="2013-12-31" maxlength="10" /><br />
  Time: <input name="Time" type="text" value="(Total Hours) Time" maxlength="20" /><br />
  Location: <input name="Location" type="text" maxlength="100" /><br />
  Description: <textarea name="Description" cols="" rows="" maxlength="1000"></textarea><br />
  Officer Student ID: <input name="OfficerStudentID" type="text" maxlength="20" /><br />
  Link: <input name="Link" type="text" maxlength="50" /><br />
  Shifts: <input name="Shifts" type="text" value="(Hours) Time, (Hours) Time, (Hours) Time" maxlength="200" /><br />
  Requirement: <input name="Requirement" type="text" maxlength="200" /><br />
  Other Question: <input name="OtherQuestion" type="text" maxlength="250" /><br />
  Active? <input name="Active" type="checkbox" value="1" checked /><br />
  <input name="addEvent" type="submit" value="Add Event" />
  <input type="hidden" name="MM_insert" value="addEvent" />
  </form>
</div>
</body>
</html>