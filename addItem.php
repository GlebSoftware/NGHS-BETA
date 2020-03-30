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

$MM_restrictGoTo = "../login.php";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addItem")) {
  $insertSQL = sprintf("INSERT INTO itemNeeds (item, itemLimit, itemExpiryDate, itemPointWorth) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['Item'], "text"),
                       GetSQLValueString($_POST['Limit'], "int"),
                       GetSQLValueString($_POST['ExpiryDate'], "date"),
                       GetSQLValueString($_POST['Point'], "double"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "addItem.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$pageTitle = 'Add An Iteam Donation Request';
$moreHead = "<script src='http://code.jquery.com/jquery-1.9.1.js'></script><script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script>";
	include('header.php')
	?>

<script>
	$(function() {
		$( "#datepicker" ).datepicker();
		$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
	});
	
</script>
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
  <h1>Add An Item Donation Request</h1>

	<div class="block">
  <form action="<?php echo $editFormAction; ?>" method="POST" name="addItem" target="_self">
  *Item: <input name="Item" type="text" maxlength="50" /><br />
  *Item Limit: <input name="Limit" type="number" step="1" min="1" value="" maxlength="20" /><br />
  *Expiry Date: <input name="ExpiryDate" type="text" value="2013-12-31" maxlength="10" id="datepicker" /><br />
  *Item Point Worth: <input name="Point" type="number" step=".25" min="0" value="" maxlength="20" /><br />
  <input name="addItem" type="submit" value="Add Item Donation Request" style="width: 100%;padding: 15px;background: #F03204;" />
  <input type="hidden" name="MM_insert" value="addItem" />
  </form>
  </div>
  
</div>

<?php

include('footer.php') 

?>
