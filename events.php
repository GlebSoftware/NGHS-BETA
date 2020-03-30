<?php require_once('Connections/nghsbeta.php'); ?>
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

$colname_upcomingEvents = "-1";
if (isset($_GET['eDate'])) {
  $colname_upcomingEvents = $_GET['eDate'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_upcomingEvents = sprintf("SELECT * FROM events WHERE (eDate >= CURDATE()) ORDER BY eDate ASC", GetSQLValueString($colname_upcomingEvents, "date"));
$upcomingEvents = mysql_query($query_upcomingEvents, $nghsbeta) or die(mysql_error());
$row_upcomingEvents = mysql_fetch_assoc($upcomingEvents);
$totalRows_upcomingEvents = mysql_num_rows($upcomingEvents);



	$pageTitle = 'Volunteer';
	include('header.php')
	?>
	
	<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	  <h1>Upcoming Beta Events</h1>
      <?php if ($totalRows_upcomingEvents > 0) { // Show if recordset not empty ?>
        <?php do { ?>
     	
     	<?php if($row_upcomingEvents['eActive'] == 1){ ?>
     	
        <a href="volunteer?eID=<?php echo $row_upcomingEvents['eID']; ?>"><div class="block" style="margin-top:30px;">
            <div class="notice" style="color: #FFF;">
              <span><?php echo date("M",strtotime($row_upcomingEvents['eDate'])); ?></span>
              <?php echo date("j",strtotime($row_upcomingEvents['eDate'])); ?>
          </div>
            <h2><?php echo $row_upcomingEvents['eName']; ?></h2>
            <em>
              <?php echo date("l\, F jS",strtotime($row_upcomingEvents['eDate'])); ?><br />
              Total Points: <?php echo strtok($row_upcomingEvents['eTime'], "?"); ?><br />
              From: <?php echo strtok("?"); ?><br />
              @ <?php echo $row_upcomingEvents['eLocation']; ?><br />
          </em>
            <p><?php echo $row_upcomingEvents['eDescription']; ?></p>
            <!--<p><em>Officer In Charge: <?php echo $row_upcomingEvents['eOfficer']; ?> </em></p>-->
            <div class="button">Sign Up</div>
        </div></a>
        
        <?php } ?>
        
        <?php } while ($row_upcomingEvents = mysql_fetch_assoc($upcomingEvents)); ?>
        <?php } // Show if recordset not empty ?>
      <?php if ($totalRows_upcomingEvents == 0) { // Show if recordset empty 
      ?>
 
  <div class="info block" style="margin-top:30px;">
    <p><em>Sorry! There are no upcoming events at the moment.</em></p>
  </div>
  <?php } // Show if recordset empty 
  ?>
<a href="items"><div class="info block" style="margin-top:30px;">
        	<h2>Item Donations</h2>
            <p>Looking for some easy Beta points? Click here to check out the items we need and signup to bring them in!</p>
        </div></a>
</div>

<?php    
include('footer.php') 
?>
<?php
mysql_free_result($upcomingEvents);
?>
