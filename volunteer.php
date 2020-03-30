<?php require_once('Connections/nghsbeta.php'); ?>
<?php

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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "volunteer" + $row_event['eName'])) {
  $insertSQL = sprintf("INSERT INTO signups (studentID, eventID, shift, otherQuestion) VALUES (%s, %s, %s, %s)",
                       $_SESSION['MM_Username'],
                       GetSQLValueString($_POST['eventID'], "int"),
                       GetSQLValueString(implode(', ', $_POST['shift']), "text"),
                       GetSQLValueString($_POST['otherQuestion'], "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "dashboard";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_event = "-1";
if (isset($_GET['eID'])) {
  $colname_event = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_event = sprintf("SELECT * FROM events WHERE eID = %s", GetSQLValueString($colname_event, "int"));
$event = mysql_query($query_event, $nghsbeta) or die(mysql_error());
$row_event = mysql_fetch_assoc($event);
$totalRows_event = mysql_num_rows($event);

$maxRows_eventSignupInfo = 10;
$pageNum_eventSignupInfo = 0;
if (isset($_GET['pageNum_eventSignupInfo'])) {
  $pageNum_eventSignupInfo = $_GET['pageNum_eventSignupInfo'];
}
$startRow_eventSignupInfo = $pageNum_eventSignupInfo * $maxRows_eventSignupInfo;

$colname_eventSignupInfo = "-1";
if (isset($_GET['eID'])) {
  $colname_eventSignupInfo = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventSignupInfo = sprintf("SELECT * FROM VWsignupInfo WHERE eventID = %s ORDER BY signupDate DESC", GetSQLValueString($colname_eventSignupInfo, "int"));
$query_limit_eventSignupInfo = sprintf("%s LIMIT %d, %d", $query_eventSignupInfo, $startRow_eventSignupInfo, $maxRows_eventSignupInfo);
$eventSignupInfo = mysql_query($query_limit_eventSignupInfo, $nghsbeta) or die(mysql_error());
$row_eventSignupInfo = mysql_fetch_assoc($eventSignupInfo);

if (isset($_GET['totalRows_eventSignupInfo'])) {
  $totalRows_eventSignupInfo = $_GET['totalRows_eventSignupInfo'];
} else {
  $all_eventSignupInfo = mysql_query($query_eventSignupInfo);
  $totalRows_eventSignupInfo = mysql_num_rows($all_eventSignupInfo);
}
$totalPages_eventSignupInfo = ceil($totalRows_eventSignupInfo/$maxRows_eventSignupInfo)-1;

$colname_myEventSignup = "-1";
if (isset($_GET['eID'])) {
  $colname_myEventSignup = $_GET['eID'];
}
$myStuID_myEventSignup = $row_memberInformation['stuID'];
if (isset($row_memberInformation['stuID'])) {
  $myStuID_myEventSignup = $row_memberInformation['stuID'];
}
$colname_myEventSignup = "-1";
if (isset($_GET['eID'])) {
  $colname_myEventSignup = $_GET['eID'];
}
$currentStuID_myEventSignup = "-1";
if (isset($_SESSION['MM_Username'])) {
  $currentStuID_myEventSignup = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_myEventSignup = sprintf("SELECT * FROM signups WHERE eventID = %s  and studentID = %s", GetSQLValueString($colname_myEventSignup, "text"),GetSQLValueString($currentStuID_myEventSignup, "text"));
$myEventSignup = mysql_query($query_myEventSignup, $nghsbeta) or die(mysql_error());
$row_myEventSignup = mysql_fetch_assoc($myEventSignup);
$totalRows_myEventSignup = mysql_num_rows($myEventSignup);

$queryString_eventSignupInfo = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_eventSignupInfo") == false && 
        stristr($param, "totalRows_eventSignupInfo") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_eventSignupInfo = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_eventSignupInfo = sprintf("&totalRows_eventSignupInfo=%d%s", $totalRows_eventSignupInfo, $queryString_eventSignupInfo);

	$pageTitle = $row_event['eName'];
	include('header.php')
	?>

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

<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1><?php echo $row_event['eName']; ?></h1>
    <div class="block">
    	<h2>Event Information</h2>
        <em>
		  <?php echo date("l\, F jS",strtotime($row_event['eDate'])); ?><br />
Total Points: <?php echo strtok($row_event['eTime'], "?"); ?><br />
          From: <?php echo strtok("?"); ?><br />
@ <?php echo $row_event['eLocation']; ?><br />
        </em>
        <p><?php echo $row_event['eDescription']; ?></p>
        <?php
        	$link = strtok($row_event['eLink'], "?");
        	$linkTitle = strtok("?");			
			?>
        <p><a href="<?php echo $link; ?>" target="_blank"><?php echo $linkTitle; ?></a></p>
        <em>Officer In Charge:<br /></em><a href="contact?id=<?php echo $row_event['eOfficerID']; ?>"><div class="button"><?php echo $row_event['eOfficer']; ?></div></a>
    </div>
    <div class="block">
    <h2>Sign Up</h2>
    <?php if ($totalRows_myEventSignup == 0) { // Show if recordset empty ?>
      <form action="<?php echo $editFormAction; ?>" method="POST" name="Sign Up<?php echo $row_event['eName']; ?>" class="volunteerForm">
        <input name="studentID" type="hidden" value="<?php echo $row_memberInformation['stuID']; ?>" />
        <input name="eventID" type="hidden" value="<?php echo $row_event['eID']; ?>" />
        <p>Please select the shifts you'd like to sign up for.</p>
        <?php
			$shifts = strtok($row_event['eShifts'], "?");
			while ($shifts != false)
			{
			?>
        <input name="shift[]" type="checkbox" value="<?php echo $shifts; ?>"  />
        <?php echo $shifts; ?><br />
        <?php
				$shifts = strtok("?");
			}
          ?>
          <?php if($row_event['eQuestion']!=NULL){ ?>
          <p><?php echo $row_event['eQuestion']; ?></p>
        <textarea name="otherQuestion" style="width:100%;" onfocus="setValue(this)" onblur="setValue(this)"></textarea>
        
        <? } else{ ?>        
        <input name="otherQuestion" type="hidden" value="0" />
        <? } ?>
        <p><em><?php echo $row_event['eRequirement']; ?></em></p>
        <input name="Sign Up" type="submit" value="Sign Up" style="width: 100%;padding: 15px;background: #F03204;" />
        <input type="hidden" name="MM_insert" value="volunteer<?php echo $row_event['eName']; ?>" />
      </form>
      <?php } // Show if recordset empty ?>
    <?php if ($totalRows_myEventSignup > 0) { // Show if recordset not empty ?>
      <em>You have already signed up for this event.</em>
      <p><strong>Shift(s): </strong><?php echo $row_myEventSignup['shift']; ?></p>
      <a href="cancelSignup?eID=<?php echo $row_myEventSignup['eventID']; ?>">Cancel My Sign-up / Change My Shift(s)</a>
      <?php } // Show if recordset not empty ?>
  </div>
    <?php if ($totalRows_eventSignupInfo > 0) { // Show if recordset not empty ?>
    <div class="info block">
        <?php if ($pageNum_eventSignupInfo > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_eventSignupInfo=%d%s", $currentPage, max(0, $pageNum_eventSignupInfo - 1), $queryString_eventSignupInfo); ?>"><div class="leftButton" style="margin-top:45px;"><</div></a>
          <?php } // Show if not first page ?>
        <?php if ($pageNum_eventSignupInfo < $totalPages_eventSignupInfo) { // Show if not last page ?> 
        <a href="<?php printf("%s?pageNum_eventSignupInfo=%d%s", $currentPage, min($totalPages_eventSignupInfo, $pageNum_eventSignupInfo + 1), $queryString_eventSignupInfo); ?>"><div class="rightButton" style="margin-top:45px;">></div></a> 
        <?php } // Show if not last page ?>
        <h2>Who Else Has Signed Up?</h2>
                <p style="text-transform:capitalize;">
        <?php do { ?>
            <?php echo $row_eventSignupInfo['fName']; ?> <?php echo $row_eventSignupInfo['lName']; ?><br />
        <?php } while ($row_eventSignupInfo = mysql_fetch_assoc($eventSignupInfo)); ?> 
        </p>
  </div>
    <?php } // Show if recordset not empty ?>

</div>

<?php    
include('footer.php') 
?>
<?php
mysql_free_result($event);
?>
