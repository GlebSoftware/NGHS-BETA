<?php require_once('Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('Connections/nghsbeta.php');
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
	
  $logoutGoTo = "index";
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

	$err = array();
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

  $updateGoTo = "dashboard";
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

$maxRows_myEvents = 7;
$pageNum_myEvents = 0;
if (isset($_GET['pageNum_myEvents'])) {
  $pageNum_myEvents = $_GET['pageNum_myEvents'];
}
$startRow_myEvents = $pageNum_myEvents * $maxRows_myEvents;

$colname_myEvents = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_myEvents = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_myEvents = sprintf("SELECT eventID, eName, eDate FROM VWsignupInfo WHERE studentID = %s and eDate >= CURDATE() ORDER BY eDate ASC", GetSQLValueString($colname_myEvents, "text"));
$query_limit_myEvents = sprintf("%s LIMIT %d, %d", $query_myEvents, $startRow_myEvents, $maxRows_myEvents);
$myEvents = mysql_query($query_limit_myEvents, $nghsbeta) or die(mysql_error());
$row_myEvents = mysql_fetch_assoc($myEvents);

if (isset($_GET['totalRows_myEvents'])) {
  $totalRows_myEvents = $_GET['totalRows_myEvents'];
} else {
  $all_myEvents = mysql_query($query_myEvents);
  $totalRows_myEvents = mysql_num_rows($all_myEvents);
}
$totalPages_myEvents = ceil($totalRows_myEvents/$maxRows_myEvents)-1;

$colname_myDonations = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_myDonations = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_myDonations = sprintf("SELECT item, itemPointWorth FROM VWdonationsInfo WHERE studentID = %s", GetSQLValueString($colname_myDonations, "text"));
$myDonations = mysql_query($query_myDonations, $nghsbeta) or die(mysql_error());
$row_myDonations = mysql_fetch_assoc($myDonations);
$totalRows_myDonations = mysql_num_rows($myDonations);

$colname_memberPoints = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_memberPoints = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_memberPoints = sprintf("SELECT * FROM points WHERE studentID = %s ORDER BY `date` ASC", GetSQLValueString($colname_memberPoints, "text"));
$memberPoints = mysql_query($query_memberPoints, $nghsbeta) or die(mysql_error());
$row_memberPoints = mysql_fetch_assoc($memberPoints);
$totalRows_memberPoints = mysql_num_rows($memberPoints);

$colname_applicant = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_applicant = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_applicant = sprintf("SELECT * FROM officerApplication WHERE studentID = %s", GetSQLValueString($colname_applicant, "int"));
$applicant = mysql_query($query_applicant, $nghsbeta) or die(mysql_error());
$row_applicant = mysql_fetch_assoc($applicant);
$totalRows_applicant = mysql_num_rows($applicant);
?>

<?php 
	$pageTitle = 'Dashboard';
	include('header.php')
	?>

<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
  <h1> Welcome, <?php echo $row_memberInformation['fName']; ?>!
    <a href="<?php echo $logoutAction ?>"><div class="button" style="vertical-align:top;">Logout</div></a>
  </h1>
<div class="block">
   	  <h2>Your Upcoming Events</h2>
   	  
          <?php if ($totalRows_myEvents > 0) { // Show if recordset not empty ?>
<?php do { ?>
              <a href="volunteer?eID=<?php echo $row_myEvents['eventID']; ?>"><div class="upcoming"><div class="upcomingDate"><span><?php echo date("M",strtotime($row_myEvents['eDate'])); ?></span><?php echo date("j",strtotime($row_myEvents['eDate'])); ?></div><span class="upcomingEvent"><?php echo $row_myEvents['eName']; ?></span></div></a><br />
            <?php } while ($row_myEvents = mysql_fetch_assoc($myEvents)); ?>
            
  <p>Click an event for more information. <a href="events">Volunteer for more events!</a></p>
  <?php } // Show if recordset not empty ?>
          <?php if ($totalRows_myEvents == 0) { // Show if recordset empty ?>
  <em>You've not volunteered for any events yet. <a href="events">Check out our upcoming events to get started.</a></em>
  <?php } // Show if recordset empty ?>
          <?php if ($totalRows_myDonations > 0) { // Show if recordset not empty ?>
  <h2>Item Donations</h2>
            <em>The items you signed up to bring in.</em>
            <p> 
              <?php do { ?>
                <?php echo $row_myDonations['item']; ?> (for <?php echo $row_myDonations['itemPointWorth']; ?> points)<br />
              <?php } while ($row_myDonations = mysql_fetch_assoc($myDonations)); ?>
</p>
          <?php } // Show if recordset not empty ?>
</div>
<!--<?php if ($totalRows_applicant > 0) { // Show if recordset not empty ?>
  <div class="info block">
    <h2>Applicant</h2>
    <strong><?php echo $row_applicant['position']; ?></strong>
    <p>You have successfully submitted the online application. <strong>Please don't forget to sign-up for an interview time outside Mrs. Carlisle's Room(#613)</strong></p>
  </div>
  <?php } else { // Show if recordset not empty ?>
  <a href="apply"><div class="info block">
    <h2>Interested in becoming a Beta Club officer?</h2>
    <p>View all the information you need and apply to be a 2015-2016 Beta Club Officer by clicking here.</p>
  </div></a>
  <?php } ?> -->
<div class="block">
  <h2>Your Points</h2>
  <?php 
  $bPoints = 0;
  $oPoints = 0;
  if ($totalRows_memberPoints > 0) { // Show if recordset not empty ?>
    <table style="width:100%;">
      <thead>
        <tr>
          <th scope="col">Date</th>
          <th scope="col">Activity / Description</th>
          <th scope="col">Beta</th>
          <th scope="col">Other</th>
          </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td><?php echo date("n-j-y",strtotime($row_memberPoints['date'])); ?></td>
            <td><?php echo $row_memberPoints['description']; ?></td>
            <td><?php echo $row_memberPoints['bPoints']; ?></td>
            <td><?php echo $row_memberPoints['oPoints']; ?></td>
          </tr>
          <?php
	 	$bPoints += $row_memberPoints['bPoints'];
	$oPoints += $row_memberPoints['oPoints'];
	
	} while ($row_memberPoints = mysql_fetch_assoc($memberPoints)); ?>
        
      </tbody>
    </table>
    <?php } // Show if recordset not empty ?>
<p>You have <strong><?php echo $bPoints; ?> </strong>Beta points and <strong><?php echo $oPoints; ?> </strong>other points (for a total of <strong><?php echo ($bPoints + $oPoints); ?> </strong>points). <em>Please record all your service hours and points on your community service logs. Points will be updated on the website after you turn in a copy of your logs at the end of the year.</em></p>
        <p>Points are cumulative.&nbsp;
            <?php
			$totalCord = 112;
			$betaCord = 56;
            if ($row_memberInformation['year']=="2020")
              {
            	  echo "As a senior, you need a total of 100 points by the end of the year to receive a cord at graduation. Of these, 50 must be Beta points.";
				  $totalPointsNeeded = 100;
				  $betaPointsNeeded = 50;
				  $totalCord = 100;
				  $betaCord = 50;
              }
            else if ($row_memberInformation['year']=="2021")
              {
	              echo "As a junior, you need a total of 80 points by the end of the year to maintain membership in Beta, of which 40 must be Beta points. To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
				  $totalPointsNeeded = 80;
				  $betaPointsNeeded = 40;
				  $totalCord = 80;
				  $betaCord = 40;
              }
            else if ($row_memberInformation['year']=="2022")
              {
	              echo "As a sophomore, you need a total of 60 points by the end of the year to maintain membership in Beta, of which 30 must be Beta points. To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
				  $totalPointsNeeded = 60;
				  $betaPointsNeeded = 30;
              }
            else if ($row_memberInformation['year']=="2023")
              {
	              echo "As a freshman, you need a total of 20 points by the end of the year to maintain membership in Beta, of which 10 must be Beta points. To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
				  $totalPointsNeeded = 20;
				  $betaPointsNeeded = 10;
              }
            else
              {
              echo "To receive a cord at graduation, you need 100 points by the end of your senior year, of which 50 must be Beta.";
              }			
            ?>
         If you are new to our school or believe this is a mistake, please contact us.</p>   
         <?php
			if (($bPoints >= $betaPointsNeeded) && (($bPoints+$oPoints) >= $totalPointsNeeded)){
				echo "<p style='color:green'>Hooray! You have enough points to maintain membership in Beta!</p>";
			}
			else{
				echo "<p style='color:red'>Currently, you do not have the required points to maintain membership in Beta or receive a cord at graduation.</p>";
			}		 
		 ?>
</div>
    <div class="block">
    	<h2>Your Profile</h2>
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
            <input name="updateProfile" type="submit" value="Update Profile" style="width: 100%;padding: 15px;background: #F03204;" />
            <input type="hidden" name="MM_update" value="updateProfile" />
        </form>
    </div>
</div>

<?php include('footer.php') ?>
<?php
mysql_free_result($myEvents);

mysql_free_result($myDonations);

mysql_free_result($memberInformation);

mysql_free_result($memberPoints);

mysql_free_result($applicant);
?>
