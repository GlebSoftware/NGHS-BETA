<?php require_once('Connections/nghsbeta.php'); 

if (!isset($_SESSION)) {
  session_start();
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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_specificMemberInfo = sprintf("SELECT * FROM beta_members", GetSQLValueString($colname_specificMemberInfo, "int"));
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
$totalRows_specificMemberPoints = mysql_num_rows($specificMemberPoints); ?>

<?php echo "test"; ?>

<h2>Your Points</h2>

<?php 
 
if($totalRows_specificMemberInfo > 0) {  ?>
	<table style="width:100%;">
      <thead>
        <tr>
        		<th scope="col">Name</th>
        </tr>
       </thead>
        <?php do { ?>
       <tbody>
       		<tr>
       			<td><?php echo $row_memberName['fName'] + " " + $row_memberName['lName'] ?></td>
       		</tr>
       </tbody>
        
<?php
  $bPoints = 0;
  $oPoints = 0;
  if ($totalRows_specificMemberInfo > 0) { // Show if recordset not empty ?>
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
            <td><?php echo date("n-j-y",strtotime($row_memberPoints['date'])); ?></td>
            <td><?php echo $row_memberPoints['description']; ?></td>
            <td><?php echo $row_memberPoints['bPoints']; ?></td>
            <td><?php echo $row_memberPoints['oPoints']; ?></td>
          </tr>
        </tbody>
        </table>
          <?php
	 	$bPoints += $row_memberPoints['bPoints'];
	$oPoints += $row_memberPoints['oPoints'];
	
	} while ($row_specificMemberPoints = mysql_fetch_assoc($specificMemberPoints))
  } while ($row_memberName = mysql_fetch_assoc($specificMemberInfo));

?>