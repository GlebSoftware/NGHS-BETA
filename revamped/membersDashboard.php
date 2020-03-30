<?php require_once('../Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('../Connections/nghsbeta.php');
require 'functions.php';

?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
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

	if(!checkEmail($_POST['email']))
	{
		$err[]='Your email is not valid!';
	}
	
	if(strlen($_POST['year'])<4 || strlen($_POST['year'])>4)
	{
		$err[]='Your graduation year should be 4 digits.';
	}

	if(strlen($_POST['phone'])<10 || strlen($_POST['phone'])>10)
	{
		$err[]='Your phone number should be 10 digits (type all 0\'s if you don\'t have one).';
	}

	if(!count($err))
	{

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateProfile")) {
  $updateSQL = sprintf("UPDATE beta_members SET email=%s, fName=%s, lName=%s, phone=%s, tshirt=%s, lunch=%s WHERE stuID=%s",
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['fName'], "text"),
                       GetSQLValueString($_POST['lName'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['tshirt'], "text"),
                       GetSQLValueString($_POST['lunch'], "text"),
                       $_SESSION['MM_Username']);

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($updateSQL, $nghsbeta) or die(mysql_error());

  $updateGoTo = "membersDashboard.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<script src="js/twitter.js"></script>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
<title>North Gwinnett Beta Club</title>
</head>

<body>
<div class="social">
	<a href="http://www.facebook.com/groups/157195107695902/" title="NG Beta Facebook Group" target="_blank"><div style="background:#3B5998;">f</div></a>
	<a href="https://twitter.com/nghsbeta" title="NG Beta Twitter Page" target="_blank"><div style="background:#4099FF;">t</div></a>
</div>
<div class="header" style="background-image:url(img/conventionBlur.jpg);">
	<div class="navBar">
    	<ul>
        	<li>News</li>
            <li>Plan</li>
            <li>Volunteer</li>
            <li class="active">Dashboard</li>
            <li>Video</li>
            <li>About</li>
            <li>Contact</li>
        </ul>
    </div>
	<h1>Welcome, <?php echo $row_memberInformation['fName']; ?>!</h1>
    <ul class="navigation">
    	<li>Upcoming</li>
        <li>Points</li>
        <li>Profile</li>
        <a href="<?php echo $logoutAction ?>"><li>Logout</li></a>
    </ul>
    <div class="container" style="padding-bottom:50px;">
    	<h2>Your Upcoming Events</h2>
        <div class="upcoming"><div class="date"><span>OCT</span>29</div><span class="event">Trunk-or-Treat</span></div><br />     
        <div class="upcoming"><div class="date"><span>NOV</span>18</div><span class="event">Beta Fast</span></div><br />
        <p>Volunteer for more events!</p><br />
    </div>
    <div class="newsBar" id="latestTweet" style="margin-top: -61px;"></div>
</div>
<div class="section" id="whyilovebeta"></div>
<div class="section gray">
	<h2><span>Your</span> Points</h2><br />
    <div class="container" style="background:none;">
        <p>You have <strong><?php echo $row_memberInformation['bPoints']; ?> </strong>Beta points and <strong><?php echo $row_memberInformation['oPoints']; ?> </strong>other points (for a total of <strong><?php echo ($row_memberInformation['bPoints']+$row_memberInformation['oPoints']); ?> </strong>points). Events from the summer through October are not included.</p>
        <p>Points are cumulative. 
			<?php
			if ($row_memberInformation['year']=="2014")
			  {
			  echo "As a senior, you need a total of 60 points by the end of the year to receive a cord at graduation. Of these, 30 must be Beta points.";
			  }
			else if ($row_memberInformation['year']=="2015")
			  {
			  echo "As a junior, you need a total of 64 points by the end of the year to maintain membership in Beta, of which 32 must be Beta points. To receive a cord at graduation, you need 92 points by the end of your senior year, of which 46 must be Beta.";
			  }
			else if ($row_memberInformation['year']=="2016")
			  {
			  echo "As a sophomore, you need a total of 40 points by the end of the year to maintain membership in Beta, of which 20 must be Beta points. To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
			  }
			else if ($row_memberInformation['year']=="2017")
			  {
			  echo "As a freshmen, you need a total of 8 points by the end of the year to maintain membership in Beta, of which 4 must be Beta points. To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
			  }
			else
			  {
			  echo "To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
			  }
			?>
         If you are new to our school or believe this is a mistake, please contact us.</p>   
    </div>
</div>
<div class="section" style="background-image:url(img/feedingTheHomelessBlur.jpg);">
	<div class="container" style="background-color:#111; width:100%;">
	<form action="<?php echo $editFormAction; ?>" method="POST" name="updateProfile" target="_self">
    	<?php
        	if($_SESSION['msg']['reg-err']){echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';unset($_SESSION['msg']['reg-err']);}
		?>

    	Student ID: <input name="stuID" type="text" value="<?php echo $row_memberInformation['stuID']; ?>" readonly="readonly" style="background:none;" /><br />
    	First Name: <input name="fName" type="text" value="<?php echo $row_memberInformation['fName']; ?>" /><br />
    	Last Name: <input name="lName" type="text" value="<?php echo $row_memberInformation['lName']; ?>" /><br />
    	Email: <input name="email" type="text" value="<?php echo $row_memberInformation['email']; ?>" /><br />
    	Graduation Year: <input name="year" type="text" value="<?php echo $row_memberInformation['year']; ?>" readonly="readonly" style="background:none;" /><br />
    	Phone: <input name="phone" type="text" value="<?php echo $row_memberInformation['phone']; ?>" /><br />
    	T-shirt: <select name="tshirt"><option value="s" <?php if (!(strcmp("s", $row_memberInformation['tshirt']))) {echo "selected=\"selected\"";} ?>>Small</option><option value="m" <?php if (!(strcmp("m", $row_memberInformation['tshirt']))) {echo "selected=\"selected\"";} ?>>Medium</option><option value="l" <?php if (!(strcmp("l", $row_memberInformation['tshirt']))) {echo "selected=\"selected\"";} ?>>Large</option><option value="xl" <?php if (!(strcmp("xl", $row_memberInformation['tshirt']))) {echo "selected=\"selected\"";} ?>>Extra Large</option></select><br/>
    	Lunch: <select name="lunch"><option value="4" selected="selected" <?php if (!(strcmp(4, $row_memberInformation['lunch']))) {echo "selected=\"selected\"";} ?>>4th</option><option value="5" <?php if (!(strcmp(5, $row_memberInformation['lunch']))) {echo "selected=\"selected\"";} ?>>5th</option><option value="6" <?php if (!(strcmp(6, $row_memberInformation['lunch']))) {echo "selected=\"selected\"";} ?>>6th</option></select><br />
        <input name="updateProfile" type="submit" value="Update Profile" style="width: 100%;padding: 15px;background: #940;" />
    	<input type="hidden" name="MM_update" value="updateProfile" />
    </form>
    </div>
</div>
<div class="section footer">
	<a href="membersDashboard.php" title="Member's Area">Member Dashboard</a><br /><br />
    &copy; North Gwinnett High School Beta Club<br />
    <a href="mailto:support@nghsbeta.com">support[at]nghsbeta.com</a> / Level Creek Road, Suwanee, GA, 30024<br />
    Ann Nicely & Sally Rutherford<br />
    Site Designed By Ashay Sheth<br />
</div>
</body>
</html>
<?php
mysql_free_result($memberInformation);
?>
