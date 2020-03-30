<?php require_once('Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('Connections/nghsbeta.php');
require 'functions.php';

?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "officer";
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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "changeMemberInfo")) {
  $updateSQL = sprintf("UPDATE beta_members SET `role`=%s WHERE stuID=%s",
                       GetSQLValueString($_POST['role'], "text"),
                       GetSQLValueString($_POST['studentID'], "int"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($updateSQL, $nghsbeta) or die(mysql_error());

  $updateGoTo = "members.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$maxRows_betaMembers = 20;
$pageNum_betaMembers = 0;
if (isset($_GET['pageNum_betaMembers'])) {
  $pageNum_betaMembers = $_GET['pageNum_betaMembers'];
}
$startRow_betaMembers = $pageNum_betaMembers * $maxRows_betaMembers;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_betaMembers = "SELECT email, stuID, fName, lName, `year`, phone, tshirt, lunch, `role` FROM beta_members ORDER BY lName ASC";
$query_limit_betaMembers = sprintf("%s LIMIT %d, %d", $query_betaMembers, $startRow_betaMembers, $maxRows_betaMembers);
$betaMembers = mysql_query($query_limit_betaMembers, $nghsbeta) or die(mysql_error());
$row_betaMembers = mysql_fetch_assoc($betaMembers);

if (isset($_GET['totalRows_betaMembers'])) {
  $totalRows_betaMembers = $_GET['totalRows_betaMembers'];
} else {
  $all_betaMembers = mysql_query($query_betaMembers);
  $totalRows_betaMembers = mysql_num_rows($all_betaMembers);
}
$totalPages_betaMembers = ceil($totalRows_betaMembers)/$maxRows_betaMembers = 20;
$pageNum_betaMembers = 0;
if (isset($_GET['pageNum_betaMembers'])) {
  $pageNum_betaMembers = $_GET['pageNum_betaMembers'];
}
$startRow_betaMembers = $pageNum_betaMembers * $maxRows_betaMembers;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_betaMembers = "SELECT email, stuID, fName, lName, `year`, phone, tshirt, lunch, `role` FROM beta_members ORDER BY lName ASC";
$query_limit_betaMembers = sprintf("%s LIMIT %d, %d", $query_betaMembers, $startRow_betaMembers, $maxRows_betaMembers);
$betaMembers = mysql_query($query_limit_betaMembers, $nghsbeta) or die(mysql_error());
$row_betaMembers = mysql_fetch_assoc($betaMembers);

if (isset($_GET['totalRows_betaMembers'])) {
  $totalRows_betaMembers = $_GET['totalRows_betaMembers'];
} else {
  $all_betaMembers = mysql_query($query_betaMembers);
  $totalRows_betaMembers = mysql_num_rows($all_betaMembers);
}
$totalPages_betaMembers = ceil($totalRows_betaMembers/$maxRows_betaMembers)-1;

$colname_specificMemberInfo = "-1";
if (isset($_GET['studentID'])) {
  $colname_specificMemberInfo = $_GET['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_specificMemberInfo = sprintf("SELECT * FROM beta_members WHERE stuID = %s", GetSQLValueString($colname_specificMemberInfo, "int"));
$specificMemberInfo = mysql_query($query_specificMemberInfo, $nghsbeta) or die(mysql_error());
$row_specificMemberInfo = mysql_fetch_assoc($specificMemberInfo);
$totalRows_specificMemberInfo = mysql_num_rows($specificMemberInfo);

$colname_specificMemberPoints = "-1";
if (isset($_GET['studentID'])) {
  $colname_specificMemberPoints = $_GET['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_specificMemberPoints = sprintf("SELECT * FROM points WHERE studentID = %s", GetSQLValueString($colname_specificMemberPoints, "text"));
$specificMemberPoints = mysql_query($query_specificMemberPoints, $nghsbeta) or die(mysql_error());
$row_specificMemberPoints = mysql_fetch_assoc($specificMemberPoints);
$totalRows_specificMemberPoints = mysql_num_rows($specificMemberPoints);

$queryString_betaMembers = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_betaMembers") == false && 
        stristr($param, "totalRows_betaMembers") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_betaMembers = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_betaMembers = sprintf("&totalRows_betaMembers=%d%s", $totalRows_betaMembers, $queryString_betaMembers);
 
	$pageTitle = 'Member Information';
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
	
	function showMemberTable()
	{
		document.getElementById('memberTable').style.display = "inline-block";
		document.getElementById('memberTableButton').style.display = "none";
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
	xmlhttp.open("GET","showsuggestions.php?url=members.php&q="+str,true);
	xmlhttp.send();
	}
	</script>


<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Member Information</h1>

    <?php if ($totalRows_specificMemberInfo > 0) { // Show if recordset not empty ?>
  <div class="block">
    <h2>Specific Member Information</h2>
    First Name: <?php echo $row_specificMemberInfo['fName']; ?><br />
    Last Name: <?php echo $row_specificMemberInfo['lName']; ?><br />
    Email: <?php echo $row_specificMemberInfo['email']; ?><br />
    Year: <?php echo $row_specificMemberInfo['year']; ?><br />
    Phone: <?php echo $row_specificMemberInfo['phone']; ?><br />
    T-shirt: <?php echo $row_specificMemberInfo['tshirt']; ?><br />
    Lunch: <?php echo $row_specificMemberInfo['lunch']; ?><br />
    Role: <?php echo $row_specificMemberInfo['role']; ?>
    <br />
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
                <td><?php echo date("n-j-y",strtotime($row_specificMemberPoints['date'])); ?></td>
                <td><?php echo $row_specificMemberPoints['description']; ?></td>
                <td><?php echo $row_specificMemberPoints['bPoints']; ?></td>
                <td><?php echo $row_specificMemberPoints['oPoints']; ?></td>
              </tr>
     <?php
	 	$bPoints += $row_specificMemberPoints['bPoints'];
	$oPoints += $row_specificMemberPoints['oPoints'];
	
	} while ($row_specificMemberPoints = mysql_fetch_assoc($specificMemberPoints)); ?>

          </tbody>
      </table>
      <br />
        Beta Points: <strong><?php echo $bPoints; ?> </strong><br />
        Other Points: <strong><?php echo $oPoints; ?> </strong><br />
        Total Points: <strong><?php echo ($bPoints + $oPoints); ?> </strong><br />
        <br />
        <a href="givePoints?studentID=<?php echo $row_specificMemberInfo['stuID']; ?>"><div class="button">Give Points</div></a>
  </div>
  <?php } // Show if recordset not empty ?>

    
    <div class="content block" style="text-align:center; display:none;" id="memberTable">
    <table style="width:100%;">
          <thead>
              <tr>
                <th scope="col">Last Name</th>
                  <th scope="col">First Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Phone</th>
                  <th scope="col">Class</th>
                  <th scope="col">Lunch</th>
                  <th scope="col">T-Shirt</th>
              </tr>
        </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td><?php echo $row_betaMembers['lName']; ?></td>
                <td><?php echo $row_betaMembers['fName']; ?></td>
                <td><?php echo $row_betaMembers['email']; ?></td>
                <td><?php echo $row_betaMembers['phone']; ?></td>
                <td><?php echo $row_betaMembers['year']; ?></td>
                <td><?php echo $row_betaMembers['lunch']; ?></td>
                <td><?php echo $row_betaMembers['tshirt']; ?></td>
              </tr>
              <?php } while ($row_betaMembers = mysql_fetch_assoc($betaMembers)); ?>
          </tbody>
      </table>
      <p><a href="<?php printf("%s?pageNum_betaMembers=%d%s", $currentPage, max(0, $pageNum_betaMembers - 1), $queryString_betaMembers); ?>">Previous Page</a> | <a href="<?php printf("%s?pageNum_betaMembers=%d%s", $currentPage, min($totalPages_betaMembers, $pageNum_betaMembers + 1), $queryString_betaMembers); ?>">Next Page</a></p>    	
    </div>
    <div class="info block" onclick="showMemberTable();" style="cursor:pointer;" id="memberTableButton">
    	<h2>See All Members' Information</h2>
    </div>
    <div class="info block">
    	<h2>Search Members</h2>
      <form method="get" name="studentID" class="fullForm">
       	  <input name="studentID" type="text" maxlength="20" value="Name or Student ID" onfocus="setValue(this)" onblur="setValue(this)" style="font-size:20px; text-align:center;" onkeyup="showSuggestions(this.value)"/><br />
      </form>
      	<div id="suggestions"></div>
    </div>

<div class="block">
    	<h2>Promote Members</h2>
        <form action="<?php echo $editFormAction; ?>" method="POST" name="changeMemberInfo">
        	Student ID: <input name="studentID" type="text" /><br />
            Role: <select name="role"><option value="member" selected="selected">Member</option><option value="team">Leadership Team</option><option value="officer">Officer</option></select><br />
            <input name="changeInfo" type="submit" value="Submit!" style="width: 100%;padding: 15px;background: #F03204;"/>
            <input type="hidden" name="MM_update" value="changeMemberInfo" />
        </form>    
    </div>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($betaMembers);

mysql_free_result($specificMemberInfo);

mysql_free_result($specificMemberPoints);
?>
