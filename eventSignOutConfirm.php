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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "pointConfirm")) {
  $updateSQL = sprintf("UPDATE points SET bPoints=%s WHERE studentID=%s AND eID=%s",
                       GetSQLValueString($_POST['points'], "double"),
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['eID'], "int"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($updateSQL, $nghsbeta) or die(mysql_error());

  $updateGoTo = "eventSignIn";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_pointConfirm = "-1";
if (isset($_GET['sID'])) {
  $colname_pointConfirm = $_GET['sID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_pointConfirm = sprintf("SELECT * FROM points WHERE studentID = %s ORDER BY eID ASC", GetSQLValueString($colname_pointConfirm, "text"));
$pointConfirm = mysql_query($query_pointConfirm, $nghsbeta) or die(mysql_error());
$row_pointConfirm = mysql_fetch_assoc($pointConfirm);
$totalRows_pointConfirm = mysql_num_rows($pointConfirm);


	$pageTitle = 'Event Sign-Out Confirm';
	include('header.php')


?>


<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<div class="block">
   	  <h2>Sign-Out Confirm</h2>
        <?php do { 
		if($row_pointConfirm['eID'] == $_GET["eID"]){
		?>
        <form ACTION="<?php echo $editFormAction; ?>" method="POST" name="pointConfirm">
            Student ID: <input name="studentID" type="text" readonly="true" value="<?php echo $row_pointConfirm['studentID']; ?>"><br />
            Sign-in: <input name="signIn" type="text" readonly="true" value="<?php echo $row_pointConfirm['signIn']; ?>"><br />
            Sign-out: <input name="signOut" type="text" readonly="true" value="<?php echo $row_pointConfirm['signOut']; ?>"><br />
            <?php
				$hours = abs(strtotime($row_pointConfirm['signOut']) - strtotime($row_pointConfirm['signIn'])) / 3600;
				$hours += .15;
				$points = floor($hours * 2) / 2;
			?>
            Points: <input name="points" type="text" readonly="true" value="<?php echo $points; ?>"><br />
            <input name="eID" type="hidden" value="<?php echo $row_pointConfirm['eID']; ?>">
            <input name="confirm" type="submit" value="Confirm" style="width: 100%;padding: 15px;background: #F03204;">
            <input type="hidden" name="MM_update" value="pointConfirm">
        </form>
          <?php } } while ($row_pointConfirm = mysql_fetch_assoc($pointConfirm)); ?>
    </div>
</div>

<?php include('footer.php') 

?>
<?php
mysql_free_result($pointConfirm);
?>
