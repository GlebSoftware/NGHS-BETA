<?php require_once('Connections/nghsbeta.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == $row_itemNeeds['item'] + "donation")) {
  $insertSQL = sprintf("INSERT INTO itemDonations (itemID, studentID) VALUES (%s, %s)",
                       GetSQLValueString($_POST['itemID'], "int"),
                       $_SESSION['MM_Username']);
  						

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "dashboard";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_itemNeeds = "SELECT * FROM itemNeeds WHERE itemExpiryDate >= CURDATE() ORDER BY itemExpiryDate ASC";
$itemNeeds = mysql_query($query_itemNeeds, $nghsbeta) or die(mysql_error());
$row_itemNeeds = mysql_fetch_assoc($itemNeeds);
$totalRows_itemNeeds = mysql_num_rows($itemNeeds);

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_itemsReceived = "SELECT itemID FROM itemDonations ORDER BY itemID ASC";
$itemsReceived = mysql_query($query_itemsReceived, $nghsbeta) or die(mysql_error());
$row_itemsReceived = mysql_fetch_assoc($itemsReceived);
$totalRows_itemsReceived = mysql_num_rows($itemsReceived);

	$pageTitle = 'Item Needs';
	include('header.php')
	?>

	
	<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	  <h1>Beta Item Needs</h1>
  <div class="block">
        <?php if (($totalRows_itemNeeds > 0)&&(isset($_SESSION['MM_Username']))) {
  $colname_memberInformation = $_SESSION['MM_Username']; // Show if recordset not empty ?>
    <p>Please bring items to Mrs. Johnston in room 912 or Mrs. Simmons in room 210 in a plastic bag with a sticky note on it, containing your</p>
    <p>First Name</p>
    <p>Last Name</p>
    <p>Student Number</p>
    <h3>Thank you!</h3>
    <?php do { ?>
    <?php
		$numberDonated=0;
		mysql_data_seek($itemsReceived,0);
		do {
		if($row_itemNeeds['itemID']==$row_itemsReceived['itemID']){
			$numberDonated += 1;
		}
		} while ($row_itemsReceived = mysql_fetch_assoc($itemsReceived));
		if($numberDonated < $row_itemNeeds['itemLimit']){
	?>
      <form action="<?php echo $editFormAction; ?>" method="POST" name="<?php echo $row_itemNeeds['item']; ?>donation">
        <input name="itemID" type="hidden" value="<?php echo $row_itemNeeds['itemID']; ?>" />
        <input name="" type="submit" value="<?php echo $row_itemNeeds['item']; ?> (by <?php echo date("F jS",strtotime($row_itemNeeds['itemExpiryDate'])); ?> for <?php echo $row_itemNeeds['itemPointWorth']; ?> points per)" style="width: 100%;padding: 15px;background: #F03204;" />
        <input type="hidden" name="MM_insert" value="<?php echo $row_itemNeeds['item']; ?>donation" />
      </form>
      <?php } } while ($row_itemNeeds = mysql_fetch_assoc($itemNeeds)); ?>
  <?php } else{ // Show if recordset not empty ?>
  <em>Sorry! There are no item needs at the moment.</em>
  <?php } ?>
  </div>
</div>
<?php    
include('footer.php') 
?>
<?php
mysql_free_result($itemNeeds);

mysql_free_result($itemsReceived);
?>
