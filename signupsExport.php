<?php require_once('Connections/nghsbeta.php'); ?>
<?PHP
  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

if (!isset($_GET["eID"])) {
  header("Location: admin"); 
}

require_once('Connections/nghsbeta.php');

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "officer,team";
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

$colname_eventSignups = "-1";
if (isset($_GET['eID'])) {
  $colname_eventSignups = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventSignups = sprintf("SELECT shift, otherQuestion, signupDate, email, fName, lName, `year`, phone, tshirt, lunch FROM VWsignupInfo WHERE eventID = %s ORDER BY signupDate ASC", GetSQLValueString($colname_eventSignups, "int"));
$eventSignups = mysql_query($query_eventSignups, $nghsbeta) or die(mysql_error());
$row_eventSignups = mysql_fetch_assoc($eventSignups);
$totalRows_eventSignups = mysql_num_rows($eventSignups);

$colname_eventInfo = "-1";
if (isset($_GET['eID'])) {
  $colname_eventInfo = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventInfo = sprintf("SELECT * FROM events WHERE eID = %s", GetSQLValueString($colname_eventInfo, "int"));
$eventInfo = mysql_query($query_eventInfo, $nghsbeta) or die(mysql_error());
$row_eventInfo = mysql_fetch_assoc($eventInfo);
$totalRows_eventInfo = mysql_num_rows($eventInfo);


  function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // filename for download
  $filename = $row_eventInfo['eName'] . " Signups " . date('m-d-Y') . ".xls";



  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: application/vnd.ms-excel");
  

    do{
	if(!$flag) {
      // display field/column names as first row
      echo implode("\t", array_keys($row_eventSignups)) . "\r\n";
      $flag = true;
    }
    array_walk($row_eventSignups, 'cleanData');
    echo implode("\t", array_values($row_eventSignups)) . "\r\n";
	} while ($row_eventSignups = mysql_fetch_assoc($eventSignups));


exit;
?>
<?php
mysql_free_result($eventSignups);
?>
