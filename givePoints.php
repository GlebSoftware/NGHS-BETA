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

$colname_addedMemberInfo = "-1";
if (isset($_GET['studentID'])) {
  $colname_addedMemberInfo = $_GET['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_addedMemberInfo = sprintf("SELECT fName, lName FROM beta_members WHERE stuID = %s", GetSQLValueString($colname_addedMemberInfo, "int"));
$addedMemberInfo = mysql_query($query_addedMemberInfo, $nghsbeta) or die(mysql_error());
$row_addedMemberInfo = mysql_fetch_assoc($addedMemberInfo);
$totalRows_addedMemberInfo = mysql_num_rows($addedMemberInfo);

$colname_addedMemberPoints = "-1";
if (isset($_GET['studentID'])) {
  $colname_addedMemberPoints = $_GET['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_addedMemberPoints = sprintf("SELECT bPoints, oPoints, `description`, `date` FROM points WHERE studentID = %s ORDER BY `date` ASC", GetSQLValueString($colname_addedMemberPoints, "text"));
$addedMemberPoints = mysql_query($query_addedMemberPoints, $nghsbeta) or die(mysql_error());
$row_addedMemberPoints = mysql_fetch_assoc($addedMemberPoints);
$totalRows_addedMemberPoints = mysql_num_rows($addedMemberPoints);

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "givePoints")) {
  $insertSQL = sprintf("INSERT INTO points (studentID, bPoints, oPoints, `description`, `date`) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['studentID'], "text"),
                       GetSQLValueString($_POST['bPoints'], "double"),
                       GetSQLValueString($_POST['oPoints'], "double"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['Date'], "date"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "givePoints";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


	$pageTitle = 'Give Points';
	include('header.php')

?>
<script>
	$(function() {
		$( "#datepicker" ).datepicker();
		$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
	});
	</script>

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
	xmlhttp.open("GET","showsuggestions.php?url=givePoints&q="+str,true);
	xmlhttp.send();
	}
	
		$(function() {
		$( "#datepicker" ).datepicker();
		$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
	});
	
    </script>


    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Give Points</h1>
    <div class="info block">
    	<h2>Find Member</h2>
      <form method="get" name="studentID" class="fullForm">
       	  <input name="studentID" type="text" maxlength="20" value="Name or Student ID" onfocus="setValue(this)" onblur="setValue(this)" style="font-size:20px; text-align:center;" onkeyup="showSuggestions(this.value)"/><br />
      </form>
      	<div id="suggestions"></div>
    </div>    
    <?php if ($totalRows_addedMemberInfo > 0) { // Show if recordset not empty ?>
    <div class="block">
        <h2><?php echo $row_addedMemberInfo['fName']; ?> <?php echo $row_addedMemberInfo['lName']; ?> - Points</h2>
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
                <td><?php echo date("n-j-y",strtotime($row_addedMemberPoints['date'])); ?></td>
                <td><?php echo $row_addedMemberPoints['description']; ?></td>
                <td><?php echo $row_addedMemberPoints['bPoints']; ?></td>
                <td><?php echo $row_addedMemberPoints['oPoints']; ?></td>
            </tr>
            <?php
	 	$bPoints += $row_addedMemberPoints['bPoints'];
	$oPoints += $row_addedMemberPoints['oPoints'];
	      } while ($row_addedMemberPoints = mysql_fetch_assoc($addedMemberPoints)); ?>
          </tbody>
        </table>
        <br />
        Beta Points: <strong><?php echo $bPoints; ?></strong><br />
        Other Points: <strong><?php echo $oPoints; ?></strong><br />
        Total Points: <strong><?php echo ($bPoints + $oPoints); ?></strong>
  </div>    
    <?php } // Show if recordset not empty ?>
    <div class="block">
    	<form action="<?php echo $editFormAction; ?>" method="POST" name="givePoints">
        	Student ID: <input name="studentID" type="text" maxlength="20" value="<?php echo($_GET['studentID']); ?>" /><br />
            Beta Points: <input name="bPoints" type="number" value="0" step=".5" maxlength="10" /><br />
            Other Points <input name="oPoints" type="number" value="0" step=".5" maxlength="10" /><br />
            Event Name or Description: <input name="description" type="text" value="" maxlength="50" /><br />
            Event Date(YY-MM-DD): <input name="Date" type="text" value="" maxlength="10" id="datepicker" /><br />
            <input name="givePoints" type="submit" value="Give Points" style="width: 100%;padding: 15px;background: #F03204;" />
            <input type="hidden" name="MM_insert" value="givePoints" />
        </form>
    </div>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($addedMemberInfo);

mysql_free_result($addedMemberPoints);
?>
