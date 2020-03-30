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
$query_officerApplications = "SELECT * FROM officerApplication ORDER BY `position` ASC";
$officerApplications = mysql_query($query_officerApplications, $nghsbeta) or die(mysql_error());
$row_officerApplications = mysql_fetch_assoc($officerApplications);
$totalRows_officerApplications = mysql_num_rows($officerApplications);

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_serviceApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Service VP'";
$serviceApplicant = mysql_query($query_serviceApplicant, $nghsbeta) or die(mysql_error());
$totalRows_serviceApplicant = mysql_num_rows($serviceApplicant);
$count = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_historianApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Historian'";
$historianApplicant = mysql_query($query_historianApplicant, $nghsbeta) or die(mysql_error());
$totalRows_historianApplicant = mysql_num_rows($historianApplicant);
$count1 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_treasurerApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Treasurer'";
$treasurerApplicant = mysql_query($query_treasurerApplicant, $nghsbeta) or die(mysql_error());
$totalRows_treasurerApplicant = mysql_num_rows($treasurerApplicant);
$count2 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_fundApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Fundraising VP'";
$fundApplicant = mysql_query($query_fundApplicant, $nghsbeta) or die(mysql_error());
$totalRows_fundApplicant = mysql_num_rows($fundApplicant);
$count3 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_comApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Secretary of Communications & Public Relations'";
$comApplicant = mysql_query($query_comApplicant, $nghsbeta) or die(mysql_error());
$totalRows_comApplicant = mysql_num_rows($comApplicant);
$count4 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_techApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Technology Coordinator and Website Administrator'";
$techApplicant = mysql_query($query_techApplicant, $nghsbeta) or die(mysql_error());
$totalRows_techApplicant = mysql_num_rows($techApplicant);
$count5 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_meetingApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Meeting Coordinator'";
$meetingApplicant = mysql_query($query_meetingApplicant, $nghsbeta) or die(mysql_error());
$totalRows_meetingApplicant = mysql_num_rows($meetingApplicant);
$count6 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_membershipApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Secretary of Membership'";
$membershipApplicant = mysql_query($query_membershipApplicant, $nghsbeta) or die(mysql_error());
$totalRows_membershipApplicant = mysql_num_rows($membershipApplicant);
$count7 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_seniorApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Senior Level Grade Representative'";
$seniorApplicant = mysql_query($query_seniorApplicant, $nghsbeta) or die(mysql_error());
$totalRows_seniorApplicant = mysql_num_rows($seniorApplicant);
$count8 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_juniorApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Junior Level Grade Representative'";
$juniorApplicant = mysql_query($query_juniorApplicant, $nghsbeta) or die(mysql_error());
$totalRows_juniorApplicant = mysql_num_rows($juniorApplicant);
$count9 = 1;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_sophApplicant = "SELECT * FROM officerApplication WHERE `position` = 'Sophomore Level Grade Representative'";
$sophApplicant = mysql_query($query_sophApplicant, $nghsbeta) or die(mysql_error());
$totalRows_sophApplicant = mysql_num_rows($sophApplicant);
$count10 = 1;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
    <meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
    <meta name="viewport" content="width=device-width" />
    <title>Applicant Breakdown | North Gwinnett Beta Club</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css'>
  </head>
<body style="background:#fff; max-width:1000px; padding:50px;">

<h2>Service VP | <?php echo $totalRows_serviceApplicant; ?></h2>	
<?php while($row_serviceApplicant = mysql_fetch_assoc($serviceApplicant)) { 
	
	echo "<strong>", $count, ". </strong>", $row_serviceApplicant['studentName'], "</br>";
	$count++;
	
} ?>

<h2>Historian | <?php echo $totalRows_historianApplicant; ?></h2>
<?php while($row_historianApplicant = mysql_fetch_assoc($historianApplicant)) { 
	echo "<strong>", $count1, ". </strong>", $row_historianApplicant['studentName'], "</br>";
	$count1++;
} ?>

<h2>Treasurer | <?php echo $totalRows_treasurerApplicant; ?></h2>
<?php while($row_treasurerApplicant = mysql_fetch_assoc($treasurerApplicant)) { 
	echo "<strong>", $count2, ". </strong>", $row_treasurerApplicant['studentName'], "</br>";
	$count2++;
} ?>

<h2>Fundraising VP | <?php echo $totalRows_fundApplicant; ?></h2>
<?php while($row_fundApplicant = mysql_fetch_assoc($fundApplicant)) { 
	echo "<strong>", $count3, ". </strong>", $row_fundApplicant['studentName'], "</br>";
	$count3++;
} ?>

<h2>Secretary of Communications & Public Relations | <?php echo $totalRows_comApplicant; ?></h2>
<?php while($row_comApplicant = mysql_fetch_assoc($comApplicant)) { 
	echo "<strong>", $count4, ". </strong>", $row_comApplicant['studentName'], "</br>";
	$count4++;
} ?>

<h2>Technology Coordinator | <?php echo $totalRows_techApplicant; ?></h2>
<?php while($row_techApplicant = mysql_fetch_assoc($techApplicant)) { 
	echo "<strong>", $count5, ". </strong>", $row_techApplicant['studentName'], "</br>";
	$count5++;
} ?>

<h2>Meeting Coordinator | <?php echo $totalRows_meetingApplicant; ?></h2>
<?php while($row_meetingApplicant = mysql_fetch_assoc($meetingApplicant)) { 
	echo "<strong>", $count6, ". </strong>", $row_meetingApplicant['studentName'], "</br>";
	$count6++;
} ?>

<h2>Secretary of Membership | <?php echo $totalRows_membershipApplicant; ?></h2>
<?php while($row_membershipApplicant = mysql_fetch_assoc($membershipApplicant)) { 
	echo "<strong>", $count7, ". </strong>", $row_membershipApplicant['studentName'], "</br>";
	$count7++;
} ?>

<h2>Senior Level Grade Representative | <?php echo $totalRows_seniorApplicant; ?></h2>
<?php while($row_seniorApplicant = mysql_fetch_assoc($seniorApplicant)) { 
	echo "<strong>", $count8, ". </strong>", $row_seniorApplicant['studentName'], "</br>";
	$count8++;
} ?>

<h2>Junior Level Grade Representative | <?php echo $totalRows_juniorApplicant; ?></h2>
<?php while($row_juniorApplicant = mysql_fetch_assoc($juniorApplicant)) { 
	echo "<strong>", $count9, ". </strong>", $row_juniorApplicant['studentName'], "</br>";
	$count9++;
} ?>

<h2>Sophomore Level Grade Representative | <?php echo $totalRows_sophApplicant; ?></h2>
<?php while($row_sophApplicant = mysql_fetch_assoc($sophApplicant)) { 
	echo "<strong>", $count10, ". </strong>", $row_sophApplicant['studentName'], "</br>";
	$count10++;
} ?>



<!--<?php while(($row_officerApplications2 = mysql_fetch_assoc($officerApplications))){?>	
	
	<?php if($row_officerApplications2['position'] != "President") { ?>
	<strong><?php echo $row_officerApplications2['studentName']; ?></strong> | <?php echo $row_officerApplications2['position']; ?></br>
	<?php } ?>

<?php } ?> -->

	
</body>
</html>
<?php
mysql_free_result($officerApplications);
?>