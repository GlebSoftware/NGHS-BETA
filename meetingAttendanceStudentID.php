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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "meetingAttendanceStudentID")) {
  $insertSQL = sprintf("INSERT INTO points (studentID, bPoints, oPoints, `description`, `date`) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['bPoints'], "double"),
                       GetSQLValueString($_POST['oPoints'], "double"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['date'], "date"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());
}

$colname_addedMemberInfo = "-1";
if (isset($_POST['studentID'])) {
  $colname_addedMemberInfo = $_POST['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_addedMemberInfo = sprintf("SELECT fName, lName FROM beta_members WHERE stuID = %s", GetSQLValueString($colname_addedMemberInfo, "int"));
$addedMemberInfo = mysql_query($query_addedMemberInfo, $nghsbeta) or die(mysql_error());
$row_addedMemberInfo = mysql_fetch_assoc($addedMemberInfo);
$totalRows_addedMemberInfo = mysql_num_rows($addedMemberInfo);


	$pageTitle = 'Meeting Attendance (Student ID)';
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
	
	function showSuggestions(str)
	{
	var xmlhttp;
	if (str.length==0)
	  { 
	  document.getElementById("suggestions").innerHTML="";
	  return;
	  }
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("suggestions").innerHTML=xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET","showsuggestions.php?url=meetingAttendanceStudentID&q="+str,true);
	xmlhttp.send();
	}
	
    </script>




<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Meeting Attendance</h1>
    <div class="block">
      <?php if ($totalRows_addedMemberInfo > 0) { // Show if recordset not empty ?>
  <p style="color:green;">Successfully signed in <?php echo $row_addedMemberInfo['fName']; ?> <?php echo $row_addedMemberInfo['lName']; ?>.</p>
  <?php } // Show if recordset not empty ?>
<form action="<?php echo $editFormAction; ?>" method="POST" name="meetingAttendanceStudentID" class="fullForm">
        	<input name="bPoints" type="hidden" value=".5">        	
        	<input name="oPoints" type="hidden" value="0">
  		    <input name="description" type="hidden" value="<?php echo date("F"); ?> Meeting">
  		    <input name="date" type="hidden" value="<?php echo date("Y-m-d"); ?>">
<!--  		    <input name="description" type="hidden" value="September Meeting">
  		    <input name="date" type="hidden" value="2013-09-12"> -->
        	<input name="studentID" type="text" maxlength="20" <?php if(isset($_GET['studentID'])){ echo "value='" . $_GET['studentID'] ."' ";} else{ ?>value="Student ID" <? } ?> onfocus="setValue(this)" onblur="setValue(this)" style="font-size:30px; text-align:center;" onkeyup="showSuggestions(this.value)"/><br />
          <input name="signIn" type="submit" value="Sign In" style="width: 100%;padding: 15px;background: #F03204;" />
          <input type="hidden" name="MM_insert" value="meetingAttendanceStudentID">
        </form><br />
      	<div id="suggestions"></div>
        
        
    </div>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($addedMemberInfo);
?>
