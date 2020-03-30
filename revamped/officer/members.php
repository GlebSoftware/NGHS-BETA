<?php require_once('../../Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('../../Connections/nghsbeta.php');
require '../functions.php';

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

  $updateGoTo = "officers.php";
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
$query_betaMembers = "SELECT email, stuID, fName, lName, `year`, bPoints, oPoints, phone, tshirt, lunch, `role` FROM beta_members ORDER BY lName ASC";
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
 
	$pageTitle = 'Members';
	include('officerHeader.php')
	?>
<div class="section" style="background-image:url(../img/conventionBlur.jpg);">
    <div class="container" style="padding-bottom:50px; width:100%;">
        <p><strong>Change Member Information</strong><br />Right now all you can do is promote members!</p>
        <form action="<?php echo $editFormAction; ?>" method="POST" name="changeMemberInfo">
        	Student ID: <input name="studentID" type="text" /><br />
            Role: <select name="role"><option value="member" selected="selected">Member</option><option value="team">Leadership Team</option><option value="officer">Officer</option></select><br />
            <input name="changeInfo" type="submit" value="Submit!" style="width: 100%;padding: 15px;background: #940;"/>
            <input type="hidden" name="MM_update" value="changeMemberInfo" />
        </form>
    </div>
</div>
<div class="section gray">
	<h2><span>Beta Member</span> Information</h2><br />
  <table>
    	<thead>
			<tr>
			  <th scope="col">Last Name</th>
				<th scope="col">First Name</th>
				<th scope="col">Email</th>
				<th scope="col">Phone</th>
				<th scope="col">Class</th>
				<th scope="col">Beta</th>
				<th scope="col">Other</th>
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
              <td><?php echo $row_betaMembers['bPoints']; ?></td>
              <td><?php echo $row_betaMembers['oPoints']; ?></td>
              <td><?php echo $row_betaMembers['lunch']; ?></td>
              <td><?php echo $row_betaMembers['tshirt']; ?></td>
            </tr>
            <?php } while ($row_betaMembers = mysql_fetch_assoc($betaMembers)); ?>
        </tbody>
    </table>
    <p><a href="<?php printf("%s?pageNum_betaMembers=%d%s", $currentPage, max(0, $pageNum_betaMembers - 1), $queryString_betaMembers); ?>">Previous Page</a> | <a href="<?php printf("%s?pageNum_betaMembers=%d%s", $currentPage, min($totalPages_betaMembers, $pageNum_betaMembers + 1), $queryString_betaMembers); ?>">Next Page</a></p>
</div>
<div class="section footer">
	<a href="index.php" title="Member's Area">Member Dashboard</a><br /><br />
    &copy; North Gwinnett High School Beta Club<br />
    <a href="mailto:support@nghsbeta.com">support[at]nghsbeta.com</a> / Level Creek Road, Suwanee, GA, 30024<br />
    Ann Nicely & Sally Rutherford<br />
    Site Designed By Ashay Sheth<br />
</div>
</body>
</html><?php
mysql_free_result($betaMembers);
?>
