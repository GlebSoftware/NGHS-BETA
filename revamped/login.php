<?php require_once('../Connections/nghsbeta.php'); ?>
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
$query_upcomingEvent = "SELECT eName, eDate FROM events WHERE eDate >= CURDATE() ORDER BY eDate LIMIT 1;";
$upcomingEvent = mysql_query($query_upcomingEvent, $nghsbeta) or die(mysql_error());
$row_upcomingEvent = mysql_fetch_assoc($upcomingEvent);
$totalRows_upcomingEvent = mysql_num_rows($upcomingEvent);mysql_select_db($database_nghsbeta, $nghsbeta);
$query_upcomingEvent = "SELECT eName, eDate FROM events WHERE eDate >= CURDATE() ORDER BY eDate LIMIT 1";
$upcomingEvent = mysql_query($query_upcomingEvent, $nghsbeta) or die(mysql_error());
$row_upcomingEvent = mysql_fetch_assoc($upcomingEvent);
$totalRows_upcomingEvent = mysql_num_rows($upcomingEvent);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "role";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_nghsbeta, $nghsbeta);
  	
  $LoginRS__query=sprintf("SELECT stuID, email, role FROM beta_members WHERE stuID=%s AND email=%s",
  GetSQLValueString($loginUsername, "int"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $nghsbeta) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'role');
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others&quot; through community service projects and fundraisers. ">
<meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<script src="js/twitter.js"></script>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
<script type="text/javascript">
function setValue(field)
{
	if(''!=field.defaultValue)
	{
		if(field.value==field.defaultValue)
		{
			field.value='';
		}
		else if(''==field.value)
		{
			field.value=field.defaultValue;
		}
	}
}
</script>
<title>North Gwinnett Beta Club</title>
</head>

<body>
<div class="social">
	<a href="http://www.facebook.com/groups/157195107695902/" title="NG Beta Facebook Group" target="_blank"><div style="background:#3B5998;">f</div></a>
	<a href="https://twitter.com/nghsbeta" title="NG Beta Twitter Page" target="_blank"><div style="background:#4099FF;">t</div></a>
</div>
<div class="navBar topBar">
    <ul>
        <li><a href="news.php" title="News">News</a></li>
        <li><a href="plan.php" title="Plan An Event">Plan</a></li>
        <li><a href="volunteer.php" title="Volunteer for Events">Volunteer</a></li>
        <li><a href="index.php" title="Dashboard">Dashboard</a></li>
        <li><a href="media.php" title="Submit a Video">Video</a></li>
        <li><a href="about.php" title="About">About</a></li>
        <li><a href="contact.php" title="Contact">Contact</a></li>
    </ul>
</div>
<div class="header">
    <div class="logo" unselectable="on">
    	<span class="title">Beta</span>
    </div>
    <ul class="navigation" style="margin:-350px 0 310px 0;">
    	<li>About</li>
        <li>Contact</li>
        <li>Calendar</li>
        <li>Submit</li>
    </ul>
    <div class="upcoming" style="margin: -125px 10px 83px;opacity: .99;filter:Alpha(opacity=99);"><div class="upcomingDate"><span><?php echo date("M",strtotime($row_upcomingEvent['eDate'])); ?></span><?php echo date("j",strtotime($row_upcomingEvent['eDate'])); ?></div><span class="upcomingEvent"><?php echo $row_upcomingEvent['eName']; ?></span></div>
	<form action="<?php echo $loginFormAction; ?>" method="POST" name="login">
   	  <input name="password" class="input" type="text" value="Email" onfocus="setValue(this)" onblur="setValue(this)" /><input name="join" class="button" type="button" value="?" /><input name="username" class="input" type="text" value="Student ID" maxlength="10" onfocus="setValue(this)" onblur="setValue(this)" /><input name="submit" class="button" type="submit" value="&gt;" />
    </form>
    <div class="newsBar" id="latestTweet">BETA NEWS</div>
</div>
<div class="section" id="whyilovebeta"></div>
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
mysql_free_result($upcomingEvent);
?>