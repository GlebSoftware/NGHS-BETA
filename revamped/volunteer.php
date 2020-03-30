<?php require_once('../Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('../Connections/nghsbeta.php');
require 'functions.php';

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

$MM_restrictGoTo = "login.php";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "volunteer" + $row_upcomingEvents['eName'])) {
  $insertSQL = sprintf("INSERT INTO signups (studentID, eventID, shift, hours, otherQuestion) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['eventID'], "int"),
                       GetSQLValueString(implode(', ', $_POST['shift']), "text"),
                       GetSQLValueString($_POST['totalHours'], "int"),
                       GetSQLValueString($_POST['otherQuestion'], "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

$maxRows_upcomingEvents = 10;
$pageNum_upcomingEvents = 0;
if (isset($_GET['pageNum_upcomingEvents'])) {
  $pageNum_upcomingEvents = $_GET['pageNum_upcomingEvents'];
}
$startRow_upcomingEvents = $pageNum_upcomingEvents * $maxRows_upcomingEvents;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_upcomingEvents = "SELECT * FROM events WHERE eDate >= CURDATE() ORDER BY eDate";
$query_limit_upcomingEvents = sprintf("%s LIMIT %d, %d", $query_upcomingEvents, $startRow_upcomingEvents, $maxRows_upcomingEvents);
$upcomingEvents = mysql_query($query_limit_upcomingEvents, $nghsbeta) or die(mysql_error());
$row_upcomingEvents = mysql_fetch_assoc($upcomingEvents);

if (isset($_GET['totalRows_upcomingEvents'])) {
  $totalRows_upcomingEvents = $_GET['totalRows_upcomingEvents'];
} else {
  $all_upcomingEvents = mysql_query($query_upcomingEvents);
  $totalRows_upcomingEvents = mysql_num_rows($all_upcomingEvents);
}
$totalPages_upcomingEvents = ceil($totalRows_upcomingEvents/$maxRows_upcomingEvents)-1;
?>
<?php 
	$pageTitle = 'Volunteer';
	include('header.php')
	?>

<div class="header" style="background-image:url(img/conventionBlur.jpg);">
  <h1>Volunteer for events!</h1>
    <div class="container" style="padding-bottom:50px;">
    	<h2>Your Events</h2>
  </div>
</div>
<div class="section gray">
	<h2><span>Upcoming </span>Events</h2><br />
    <div class="container" style="background:none; color:#000;">
      <?php do { ?>
      	<div class="event">
	        <h1><?php echo $row_upcomingEvents['eName']; ?></h1>
   		    <p><strong><?php echo $row_upcomingEvents['eDate']; ?></strong> from <?php echo $row_upcomingEvents['eTime']; ?></p>
   	    	<p>@ <?php echo $row_upcomingEvents['eLocation']; ?></p>
        	<p><?php echo $row_upcomingEvents['eDescription']; ?></p>
            <p style="font-style:italic;"><?php echo $row_upcomingEvents['eRequirement']; ?></p>
            <form action="<?php echo $editFormAction; ?>" method="POST" name="volunteer<?php echo $row_upcomingEvents['eName']; ?>" style="color:#000">
            	<input name="studentID" type="hidden" value="<?php echo $row_memberInformation['stuID']; ?>" />
                <input name="eventID" type="hidden" value="<?php echo $row_upcomingEvents['eID']; ?>" />
                <?php
				  $token = strtok($row_upcomingEvents['eShifts'], "?");
				  $totalHours = 0;
                	while ($token != false)
					{
						$totalHours += $token;
						$token = strtok("?");
				?>
                <input name="shift[]" type="checkbox" value="<?php echo $token; ?>"  /><?php echo $token; ?><br />
                <?php
						$token = strtok("?");
					}
				?>
                <input name="totalHours" type="hidden" value="<?php echo $totalHours; ?>" />
                <input name="otherQuestion" type="hidden" value="0" />
                <input name="Volunteer" type="submit" value="Volunteer" />
                <input type="hidden" name="MM_insert" value="volunteer<?php echo $row_upcomingEvents['eName']; ?>" />
            </form>
            <div class="button">Volunteer!</div>
        </div>
      <?php } while ($row_upcomingEvents = mysql_fetch_assoc($upcomingEvents)); ?>
    </div>
</div>
<div class="section footer">
	<a href="index.php" title="Member's Area">Member Dashboard</a><br /><br />
    &copy; North Gwinnett High School Beta Club<br />
    <a href="mailto:support@nghsbeta.com">support[at]nghsbeta.com</a> / Level Creek Road, Suwanee, GA, 30024<br />
    Ann Nicely & Sally Rutherford<br />
    Site Designed By Ashay Sheth<br />
</div>
</body>
</html>
<?php
mysql_free_result($memberInformation);

mysql_free_result($upcomingEvents);
?>
