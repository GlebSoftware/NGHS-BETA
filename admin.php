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

$maxRows_events = 5;
$pageNum_events = 0;
if (isset($_GET['pageNum_events'])) {
  $pageNum_events = $_GET['pageNum_events'];
}
$startRow_events = $pageNum_events * $maxRows_events;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_events = "SELECT eID, eName, eDate FROM events ORDER BY eDate DESC";
$query_limit_events = sprintf("%s LIMIT %d, %d", $query_events, $startRow_events, $maxRows_events);
$events = mysql_query($query_limit_events, $nghsbeta) or die(mysql_error());
$row_events = mysql_fetch_assoc($events);

if (isset($_GET['totalRows_events'])) {
  $totalRows_events = $_GET['totalRows_events'];
} else {
  $all_events = mysql_query($query_events);
  $totalRows_events = mysql_num_rows($all_events);
}
$totalPages_events = ceil($totalRows_events/$maxRows_events)-1;

	$pageTitle = 'Admin';
	include('header.php')
	?>
	
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Beta Officer Area</h1>
    <a href="addEvent"><div class="info block">
    	<h2>Project Submit</h2>
        <p>Get events on the website</p>
    </div></a>
    <div class="block">
        <?php if ($pageNum_events > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_events=%d%s", $currentPage, max(0, $pageNum_events - 1), $queryString_events); ?>"><div class="leftButton" style="margin-top:45px;"><</div></a>
          <?php } // Show if not first page ?>
      <?php if ($pageNum_events < $totalPages_events) { // Show if not last page ?> 
      <a href="<?php printf("%s?pageNum_events=%d%s", $currentPage, min($totalPages_events, $pageNum_events + 1), $queryString_events); ?>"><div class="rightButton" style="margin-top:45px;">></div></a> 
        <?php } // Show if not last page ?>    
    <h2>View Event Signups</h2>
        <p>Please select an event to view signups for.</p>
        <?php do { ?>
        <a href="signups?eID=<?php echo $row_events['eID']; ?>"><div class="button"><?php echo $row_events['eName']; ?></div></a>
        <?php } while ($row_events = mysql_fetch_assoc($events)); ?>
    </div>
    <a href="givePoints"><div class="info block">
    	<h2>Give Points</h2>
        <p>Award members beta and non-beta points.</p>
    </div></a>
    <div class="info block">
    	<h2>Meeting Attendance</h2>
        <a href="meetingAttendanceStudentID"><div class="button">Student ID</div></a>
        <a href="meetingAttendanceList"><div class="button">Names Listed</div></a>
    </div>
    <a href="logCheck"><div class="info block">
    	<h2>Service Log Check</h2>
        <p>Update members' points with collected service logs.</p>
    </div></a>
    <a href="post"><div class="info block">
    	<h2>Create News Post</h2>
        <p>Add an article under the "news" page. The most recently added article appears on the home page.</p>
    </div></a>
    <a href="members.php"><div class="info block">
    	<h2>View Members</h2>
        <p>View all NG Beta members who have registered online. Promote members to officer and leadership team roles.</p>
    </div></a>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($events);
?>
