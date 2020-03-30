<?php require_once('Connections/nghsbeta.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_GET["eID"])) {
  header("Location: admin"); 
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "signIn")) {
  $insertSQL = sprintf("INSERT INTO points (studentID, bPoints, oPoints, `description`, `date`, eID) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['bPoints'], "double"),
                       GetSQLValueString($_POST['oPoints'], "double"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['eID'], "int"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "signOut")) {
  $updateSQL = sprintf("UPDATE points SET signOut=%s WHERE studentID=%s AND eID=%s",
                       GetSQLValueString($_POST['signOut'], "date"),
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['eID'], "int"));


  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($updateSQL, $nghsbeta) or die(mysql_error());
  
  $updateGoTo = "eventSignOutConfirm?sID=" . $_POST['studentID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_eventSignups = "-1";
if (isset($_GET['eID'])) {
  $colname_eventSignups = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventSignups = sprintf("SELECT * FROM VWsignupInfo WHERE eventID = %s ORDER BY fName ASC", GetSQLValueString($colname_eventSignups, "int"));
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


$colname_addedMemberInfo = "-1";
if (isset($_POST['studentID'])) {
  $colname_addedMemberInfo = $_POST['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_addedMemberInfo = sprintf("SELECT fName, lName FROM beta_members WHERE stuID = %s", GetSQLValueString($colname_addedMemberInfo, "int"));
$addedMemberInfo = mysql_query($query_addedMemberInfo, $nghsbeta) or die(mysql_error());
$row_addedMemberInfo = mysql_fetch_assoc($addedMemberInfo);
$totalRows_addedMemberInfo = mysql_num_rows($addedMemberInfo);

	$pageTitle = 'Event Sign-In';
	include('header.php')
	?>
	  
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1><?php echo $row_eventInfo['eName']; ?> <strong>Sign-In</strong></h1>
	<?php if ($totalRows_addedMemberInfo > 0) { // Show if recordset not empty ?>
  <p style="color:green;">Successfully signed in <?php echo $row_addedMemberInfo['fName']; ?> <?php echo $row_addedMemberInfo['lName']; ?>.</p>
  <?php } // Show if recordset not empty ?>
    <div class="content block" style="text-align:center;">
    <table id="signups">
          <thead>
              <tr>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Shift(s)</th>
                  <?php if($row_eventInfo['eQuestion']!=0){?><th scope="col"><?php echo $row_eventInfo['eQuestion']; ?></th><?php }?>
                  <th scope="col">Phone</th>
                  <th scope="col">Sign-In</th>
                <!--  <th scope="col">Sign-Out</th> -->
              </tr>
        </thead>
          <tbody>
            <?php do { ?>
  <tr>
    <td><?php echo $row_eventSignups['fName']; ?></td>
    <td><?php echo $row_eventSignups['lName']; ?></td>
    <td><?php echo $row_eventSignups['shift']; ?></td>
    <?php if($row_eventInfo['eQuestion']!=0){?><td><?php echo $row_eventSignups['otherQuestion']; ?></td><?php }?>
    <td><?php echo $row_eventSignups['phone']; ?></td>
    <td>
    	<form action="<?php echo $editFormAction; ?>" method="POST" name="signIn">
	        <input name="studentID" type="hidden" value="<?php echo $row_eventSignups['studentID']; ?>" />
            <input name="bPoints" type="hidden" value="<?php echo strtok($row_eventInfo['eTime'], "?"); ?>" />
            <input name="oPoints" type="hidden" value="0" />
            <input name="description" type="hidden" value="<?php echo $row_eventInfo['eName']; ?>" />
            <input name="eID" type="hidden" value="<?php echo $row_eventInfo['eID']; ?>" />
            <input name="date" type="hidden" value="<?php echo $row_eventInfo['eDate']; ?>" />
            <input name="signIn" type="hidden" value="<?php echo date('Y-m-d h-m-s'); ?>" />
            <input name="signInBtn" type="submit" value="Sign-In" style="min-width:100px; background:#F03204; margin:5px; padding:20px; cursor:pointer;"/>
            <input type="hidden" name="MM_insert" value="signIn" />
        </form>
    </td>
<!--    <td>
    	<form action="<?php echo $editFormAction; ?>" method="POST" name="signOut">
	        <input name="studentID" type="hidden" value="<?php echo $row_eventSignups['studentID']; ?>" />
            <input name="eID" type="hidden" value="<?php echo $row_eventInfo['eID']; ?>" />        	
            <input name="signOut" type="hidden" value="<?php echo date('Y-m-d h-m-s'); ?>" />
            <input name="signOutBtn" type="submit" value="Sign-Out" style="min-width:100px; background:#FFF; color:#000; margin:5px; padding:20px;"/>
            <input type="hidden" name="MM_update" value="signOut" />
        </form>
    </td> -->
  </tr>
  <?php } while ($row_eventSignups = mysql_fetch_assoc($eventSignups)); ?>
          </tbody>
      </table>
  </div>
    
    
</div>

<?php include('footer.php') 

?>
<?php
mysql_free_result($eventSignups);
?>
