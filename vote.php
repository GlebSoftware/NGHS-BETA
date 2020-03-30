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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "officerElection")) {
  $insertSQL = sprintf("INSERT INTO officerElection (studentID, president, service, fundraising, meeting, sophomore, junior, senior, tech, membership, communications, treasurer, historian) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       $_SESSION['MM_Username'],
                       GetSQLValueString($_POST['president'], "text"),
                       GetSQLValueString(implode(', ', $_POST['service']), "text"),
                       GetSQLValueString($_POST['fundraising'], "text"),
                       GetSQLValueString($_POST['meeting'], "text"),
                       GetSQLValueString($_POST['sophomore'], "text"),
                       GetSQLValueString($_POST['junior'], "text"),
                       GetSQLValueString($_POST['senior'], "text"),
                       GetSQLValueString($_POST['tech'], "text"),
                       GetSQLValueString($_POST['membership'], "text"),
                       GetSQLValueString($_POST['communications'], "text"),
                       GetSQLValueString($_POST['treasurer'], "text"),
                       GetSQLValueString($_POST['historian'], "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

  $insertGoTo = "vote";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_officerCandidates = "SELECT studentName, `position` FROM officerApplication ORDER BY `position` ASC";
$officerCandidates = mysql_query($query_officerCandidates, $nghsbeta) or die(mysql_error());
$row_officerCandidates = mysql_fetch_assoc($officerCandidates);
$totalRows_officerCandidates = mysql_num_rows($officerCandidates);

$currentStuID_myVote = "-1";
if (isset($_SESSION['MM_Username'])) {
  $currentStuID_myVote = $_SESSION['MM_Username'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_myVote = sprintf("SELECT * FROM officerElection WHERE studentID = %s", GetSQLValueString($currentStuID_myVote, "text"));
$myVote = mysql_query($query_myVote, $nghsbeta) or die(mysql_error());
$row_myVote = mysql_fetch_assoc($myVote);
$totalRows_myVote = mysql_num_rows($myVote);

	$pageTitle = 'Vote';
	$moreHead = "<script src='http://code.jquery.com/jquery-1.9.1.js'></script><script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script>";
	include('header.php')
	?>
    
    <script type="text/javascript">
	jQuery(function(){
		var max = 6;
		var checkboxes = $('input[type="checkbox"]');
						   
		checkboxes.change(function(){
			var current = checkboxes.filter(':checked').length;
			checkboxes.filter(':not(:checked)').prop('disabled', current >= max);
		});
	});
	</script>
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Officer Elections</h1>
    <?php if ($totalRows_myVote == 0) { // Show if recordset empty 
    ?>
  <div class="block">
  	<p>Please choose a candidate from each position who you feel will best serve you on the 2014-2015 Beta executive board (you may choose up to 6 candidates for Service VP). Thank you!</p>
    <form action="<?php echo $editFormAction; ?>" method="POST" name="officerElection" class="volunteerForm">
      <input name="studentID" type="hidden" value="<?php echo $row_memberInformation['stuID']; ?>" />
      President<br>              
      <input name="president" type="radio" value="Jordan Thomason"> Jordan Thomason<br>
      <br>Service VP<br>              
      <input name="service[]" type="checkbox" value="Service VP">Service VP<br>
      <br>Fundraising VP<br>              
      <input name="fundraising" type="radio" value="Fundraising VP"> Fundraising VP<br>
      <br>Meeting Coordinator<br>              
      <input name="meeting" type="radio" value="Alana Watkins"> Alana Watkins<br>
      <? if($row_memberInformation['year']=='2017'){ ?>
      <br>Sophomore Level Grade Representative<br>              
      <input name="sophomore" type="radio" value="Sophomore Rep"> Sophomore Rep<br>
      <? } else if($row_memberInformation['year']=='2017'){ ?>
      <br>Junior Level Grade Representative<br>              
      <input name="junior" type="radio" value="Junior Rep"> Junior Rep<br>
      <? } else if($row_memberInformation['year']=='2016'){ ?>      
      <br>Senior Level Grade Representative<br>
      <input name="senior" type="radio" value="Senior Rep"> Senior Rep<br>              
      <? } ?>      
      <br>Technology Coordinator and Website Administrator<br>
      <input name="tech" type="radio" value="Tech Coordinator"> Tech Coordinator<br>
      <br>Secretary of Membership<br>              
      <input name="membership" type="radio" value="Josh Kim"> Josh Kim<br>
      <br>Secretary of Communications &amp; Public Relations<br>              
      <input name="communications" type="radio" value="Communications"> Communications<br>
      <br>Treasurer<br>              
      <input name="treasurer" type="radio" value="Treasurer"> Treasurer<br>
      <br>Historian<br>              
      <input name="historian" type="radio" value="Historian"> Historian<br>
      <br>
      <input name="vote" type="submit" value="Vote" style="width: 100%;padding: 15px;background: #F03204;">
      <input type="hidden" name="MM_insert" value="officerElection" />
      </form>
  </div>
  <?php } // Show if recordset empty ?>
  <?php if ($totalRows_myVote > 0) { // Show if recordset not empty 
  ?>
  <div class="block">
    <p>Thank you for voting! Your choices are included below: </p>
    President: <strong><?php echo $row_myVote['president']; ?></strong><br />
    Service VP: <strong><?php echo $row_myVote['service']; ?></strong><br />
    Fundraising VP: <strong><?php echo $row_myVote['fundraising']; ?></strong><br />
    Meeting Coordinator: <strong><?php echo $row_myVote['meeting']; ?></strong><br />
      <? if($row_memberInformation['year']=='2017'){ ?>
    Sophomore Level Grade Representative: <strong><?php echo $row_myVote['sophomore']; ?></strong><br />
      <? } else if($row_memberInformation['year']=='2016'){ ?>
    Junior Level Grade Representative: <strong><?php echo $row_myVote['junior']; ?></strong><br />
      <? } else if($row_memberInformation['year']=='2015'){ ?>      
    Senior Level Grade Representative: <strong><?php echo $row_myVote['senior']; ?></strong><br />
      <? } ?>      
    Technology Coordinator and Website Administrator: <strong><?php echo $row_myVote['tech']; ?></strong><br />
    Secretary of Membership: <strong><?php echo $row_myVote['membership']; ?></strong><br />
    Secretary of Communications &amp; Public Relations: <strong><?php echo $row_myVote['communications']; ?></strong><br />
    Treasurer: <strong><?php echo $row_myVote['treasurer']; ?></strong><br />
    Historian: <strong><?php echo $row_myVote['historian']; ?></strong>
  </div>
  <?php } // Show if recordset not empty 
  ?>
</div>

<?php include('footer.php') ?>
<?php
mysql_free_result($officerCandidates);

mysql_free_result($myVote);
?>
