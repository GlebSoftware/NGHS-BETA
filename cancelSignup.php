<?php require_once('Connections/nghsbeta.php'); 

if (!isset($_GET["eID"])) {
  header("Location: events"); 
}

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

$MM_restrictGoTo = "login";
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

$currentStuID_myEventSignup = "-1";
if (isset($_SESSION['MM_Username'])) {
  $currentStuID_myEventSignup = $_SESSION['MM_Username'];
}

$colname_myEventSignup = "-1";
if (isset($_GET['eID'])) {
  $colname_myEventSignup = $_GET['eID'];
}

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_myEventSignup = sprintf("SELECT * FROM signups WHERE eventID = %s  and studentID = %s", GetSQLValueString($colname_myEventSignup, "text"),GetSQLValueString($currentStuID_myEventSignup, "text"));
$myEventSignup = mysql_query($query_myEventSignup, $nghsbeta) or die(mysql_error());
$row_myEventSignup = mysql_fetch_assoc($myEventSignup);
$totalRows_myEventSignup = mysql_num_rows($myEventSignup);



if ((isset($row_myEventSignup['signupID'])) && ($row_myEventSignup['signupID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM signups WHERE signupID=%s",
                       GetSQLValueString($row_myEventSignup['signupID'], "int"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($deleteSQL, $nghsbeta) or die(mysql_error());

  $deleteGoTo = "volunteer?eID=" . $_GET['eID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


mysql_free_result($myEventSignup);
?>