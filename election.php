<?php require_once('Connections/nghsbeta.php'); ?>
<?php
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

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_votes = "SELECT * FROM officerElection";
$votes = mysql_query($query_votes, $nghsbeta) or die(mysql_error());
$row_votes = mysql_fetch_assoc($votes);
$totalRows_votes = mysql_num_rows($votes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
    <meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
    <meta name="viewport" content="width=device-width" />
    <title>Officer Applications | North Gwinnett Beta Club</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css'>
  </head>
<body style="background:#fff; max-width:1000px; padding:50px;">

<h1>Beta Officer Election Votes</h1>
    <?php do { ?>
      <?php echo $row_votes['studentID']; ?><br />
      <?php echo $row_votes['president']; ?><br />
      <?php echo $row_votes['service']; ?><br />
      <?php echo $row_votes['fundraising']; ?><br />
      <?php echo $row_votes['meeting']; ?><br />
      <?php echo $row_votes['sophomore']; ?><br />
      <?php echo $row_votes['junior']; ?><br />
      <?php echo $row_votes['senior']; ?><br />
      <?php echo $row_votes['tech']; ?><br />
      <?php echo $row_votes['membership']; ?><br />
      <?php echo $row_votes['communications']; ?><br />
      <?php echo $row_votes['treasurer']; ?><br />
      <?php echo $row_votes['historian']; ?><br />
      <br />
      <?php } while ($row_votes = mysql_fetch_assoc($votes)); ?>
</body>
</html>
<?php
mysql_free_result($votes);
?>
