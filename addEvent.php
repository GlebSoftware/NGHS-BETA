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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addEvent")) {
  $insertSQL = sprintf("INSERT INTO events (eName, eDate, eTime, eLocation, eDescription, eOfficerID, eOfficer, eLink, eShifts, eRequirement, eQuestion, eActive) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['Date'], "date"),
                       GetSQLValueString($_POST['Points'] . '?' . $_POST['Time'], "text"),
                       GetSQLValueString($_POST['Location'], "text"),
                       GetSQLValueString($_POST['Description'], "text"),
                       GetSQLValueString($_POST['OfficerID'], "text"),
                       GetSQLValueString($_POST['Officer'], "text"),
                       GetSQLValueString($_POST['Link'], "text"),
                       GetSQLValueString(implode('?', $_POST['Shifts']), "text"),
                       GetSQLValueString($_POST['Requirement'], "text"),
                       GetSQLValueString($_POST['OtherQuestion'], "text"),
                       GetSQLValueString(isset($_POST['Active']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "events";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$pageTitle = 'Add An Event';
$moreHead = "<script src='http://code.jquery.com/jquery-1.9.1.js'></script><script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script>";
	include('header.php')
	?>

<script>
	$(function() {
		$( "#datepicker" ).datepicker();
		$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
	});
	
	        function addFields(){
            var number = document.getElementById("numberOfShifts").value;
            var container = document.getElementById("container");
            while (container.hasChildNodes()) {
                container.removeChild(container.lastChild);
            }
            for (i=1;i<number;i++){
                container.appendChild(document.createTextNode("*Shift " + (i+1) + ": "));
                var input = document.createElement("input");
                input.type = "text";
                input.name = "Shifts[]";
                input.maxlength = "200";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));
            }
        }
	</script>
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
  <h1>Add An Event</h1>

	<div class="block">
  <form action="<?php echo $editFormAction; ?>" method="POST" name="addEvent" target="_self">
  *Event Name: <input name="Name" type="text" maxlength="50" /><br />
  *Date: <input name="Date" type="text" value="2013-12-31" maxlength="10" id="datepicker" /><br />
  *Total Possible Points: <input name="Points" type="number" step=".5" min="0" value="" maxlength="20" /><br />
  *Event Time: <input name="Time" type="text" value="" maxlength="20" /><br />
  *Location: <input name="Location" type="text" maxlength="100" /><br />
  *Description: <textarea name="Description" cols="" rows="" maxlength="1000"></textarea><br />
  *Officer ID: <input name="OfficerID" type="text" value="<?php echo $row_memberInformation['id']; ?>" maxlength="20" readonly="readonly" style="background:#000;" /><br />
  *Officer In Charge: <input name="Officer" type="text" value="<?php echo $row_memberInformation['fName'] . " " . $row_memberInformation['lName'] ?>" maxlength="50" readonly="readonly" style="background:#000;" /><br />
  Link: <input name="Link" type="text" maxlength="50" /><br />
  Number of Shifts: <input id="numberOfShifts" name="numberOfShifts" type="number" step="1" min="1" onchange="addFields()" value=""><br />
  *Shift 1: <input type="text" name="Shifts[]"><br>
  <div id="container"></div>
  Requirement: <input name="Requirement" type="text" maxlength="200" /><br />
  Other Question: <input name="OtherQuestion" type="text" maxlength="250" /><br />
  Active? <input name="Active" type="checkbox" value="1" checked /><br />
  <input name="addEvent" type="submit" value="Add Event" style="width: 100%;padding: 15px;background: #F03204;" />
  <input type="hidden" name="MM_insert" value="addEvent" />
  </form>
  </div>
  
</div>

<?php

include('footer.php') 

?>
