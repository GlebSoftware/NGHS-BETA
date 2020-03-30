<?php require_once('Connections/nghsbeta.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_GET["eID"])) {
  header("Location: admin"); 
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


$colname_eventSignups = "-1";
if (isset($_GET['eID'])) {
  $colname_eventSignups = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventSignups = sprintf("SELECT * FROM VWsignupInfo WHERE eventID = %s ORDER BY signupDate ASC", GetSQLValueString($colname_eventSignups, "int"));
$eventSignups = mysql_query($query_eventSignups, $nghsbeta) or die(mysql_error());
$row_eventSignups = mysql_fetch_assoc($eventSignups);
$totalRows_eventSignups = mysql_num_rows($eventSignups);

$colname_eventInfo = "-1";
if (isset($_GET['eID'])) {
  $colname_eventInfo = $_GET['eID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_eventInfo = sprintf("SELECT * FROM events WHERE eID = %s", GetSQLValueString($colname_eventInfo, "int"));
$eventInfo = mysql_query($query_eventInfo, $nghsbeta) or die(mysql_error());
$row_eventInfo = mysql_fetch_assoc($eventInfo);
$totalRows_eventInfo = mysql_num_rows($eventInfo);

	$pageTitle = 'Event Signups';
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
	function goBack()
  	{
 	 window.history.back()
 	}
	
	function hiddenFieldToSignups()
	{
		var signupsTable = document.getElementById('signups').innerHTML;
		var hiddenEle = document.getElementById("hiddenSignups");
		hiddenEle.value = signupsTable;
	}

    </script>

	
        
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1><?php echo $row_eventInfo['eName']; ?> <strong>Signups</strong></h1>
    <?php if ($totalRows_eventSignups > 0) { // Show if recordset not empty ?>
  <div class="content block" style="text-align:center;">
    <table id="signups">
      <thead>
        <tr>
          <th scope="col">Signup Date</th>
          <th scope="col">First Name</th>
          <th scope="col">Last Name</th>
          <th scope="col">Shift(s)</th>
          <?php if($row_eventInfo['eQuestion']!=NULL){?><th scope="col"><?php echo $row_eventInfo['eQuestion']; ?></th><?php }?>
          <th scope="col">Email</th>
          <th scope="col">Phone</th>
          <th scope="col">Class</th>
          <th scope="col">T-Shirt</th>
          <th scope="col">Lunch</th>
          </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td><?php echo date("n-j",strtotime($row_eventSignups['signupDate'])); ?></td>
            <td><?php echo $row_eventSignups['fName']; ?></td>
            <td><?php echo $row_eventSignups['lName']; ?></td>
            <td><?php echo $row_eventSignups['shift']; ?></td>
            <?php if($row_eventInfo['eQuestion']!=NULL){?><td><?php echo $row_eventSignups['otherQuestion']; ?></td><?php }?>
            <td><?php echo $row_eventSignups['email']; $allEmails .= $row_eventSignups['email'] . ", "; ?></td>
            <td><?php echo $row_eventSignups['phone']; ?></td>
            <td><?php echo $row_eventSignups['year']; ?></td>
            <td><?php echo $row_eventSignups['tshirt']; ?></td>
            <td><?php echo $row_eventSignups['lunch']; ?></td>
          </tr>
          <?php } while ($row_eventSignups = mysql_fetch_assoc($eventSignups)); ?>
      </tbody>
    </table>
  </div>
  

  <div class="info block">
  	<h2>Total Number of Signups: <em><?php echo $totalRows_eventSignups; ?></em></h2> 
	<p>Too many volunteers? Uncheck this active option to take this event off the Volunteer page, or check it to make the event reappear under the Volunteer page.</p>
 	<form action="editEvent" method="GET" name="updateEvent" target="_self">
 		Active? <input name="Active" type="checkbox" value="1" <?php if($row_eventInfo['eActive']==1){echo "checked";}?> /><br />
 		<input name="updateEvent" type="submit" value="Update Event" style="width: 100%;padding: 15px;background: #F03204; cursor: pointer;" />
 		<input type="hidden" name="eID" value="<?php echo $row_eventInfo['eID'] ?>" />
 		<input type="hidden" name="MM_update" value="UpdateEvent" /> 
 	</form>
  </div>
  
<div class="info block">
  	<h2>Email Signup Information</h2>
    <p>Send this page to someone (such as an event coordinator) if he or she requests to see who all has volunteered.</p>
<?php
function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);

  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }
if (isset($_REQUEST['email']))
  {//if "email" is filled out, proceed

  //check if the email address is invalid
  $mailcheck = spamcheck($_REQUEST['email']);
  if ($mailcheck==FALSE)
    {
    echo "<p style='color:red;'>Sorry! Please enter a valid email address. <a onclick='goBack()'>Click here to go back.</a></p>";
    }
  else
    {//send email
	$to = $_REQUEST['email'];
    $name = $row_memberInformation['fName'];
    $email = $row_memberInformation['email'];
    $subject = $row_eventInfo['eName'] . " [NG Beta Signup Information]";
	$message = "<html><body>";
    $message .= $_REQUEST['message'];
	$message .= "<br /><br />$name<br />";
	$message .= "<a href='www.nghsbeta.com'>NG Beta</a><br /><br />";
	$message .= "<h1>" . $row_eventInfo['eName'] . "</h1>";
	$message .= "<br />";
    $message .= $_REQUEST['signupsTable'];
	$message .= "</body></html>";
	$headers = '';
	$headers .= "From: $email\n";
	$headers .= "Reply-to: $email\n";
	$headers .= "BCC: $email\n";
	$headers .= "Return-Path: $email\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($to,$subject,$message,$headers);
    echo "<p style='color:green;'>The event signups information has been emailed. A copy has been emailed to you for your record. Thank you!</p>";
    }
  }
else 
  { //if "email" is not filled out, display the form 
  echo "<form method='POST' name='emailSignups' class='fullForm' onsubmit='hiddenFieldToSignups()' action='signups?eID=" . $row_eventInfo['eID'] . "'>
        	<input name='email' type='text' maxlength='50' value='Email To' onfocus='setValue(this)' onblur='setValue(this)' /><br />
            <textarea name='message' cols='' rows='10' onfocus='setValue(this)' onblur='setValue(this)'>Message</textarea><br />
            <input name='signupsTable' type='hidden' id='hiddenSignups' value=''>
            <input name='emailSignups' type='submit' value='Email Signups' style='width: 100%;padding: 15px;background: #F03204; cursor: pointer;' />
  </form>";
  }
  ?>    	
    
  </div>
  
  <div class="info block">
  	<h2>Email Volunteers</h2>
    <p><em>Copy/paste the volunteers' email addresses below into the bcc line of an email.</em></p>
    <form name="emailVolunteers" class="fullForm">
    	<textarea name="allEmails" cols="" rows="20" onclick="this.select();"><?php echo $allEmails; ?></textarea>
    </form>
  </div>

  <a href="eventSignIn?eID=<? echo $_GET["eID"]; ?>"><div class="info block">
  	<h2><?php echo $row_eventInfo['eName'] ?> | Event Sign-in</h2>
  </div></a>
  
  <div class="info block">
  	<h2>Download</h2>
    <a href="signupsExport?eID=<? echo $_GET["eID"]; ?>"><div class="button"><?php echo $row_eventInfo['eName'] . " Signups " . date('m-d-Y') . ".xls"; ?></div></a>
  </div>
  <?php } // Show if recordset not empty 
  else {
  ?>
  
  <div class="info block">
  	<h2>Sorry!</h2>
    <p>Nobody has signed up for this event yet. Post it on the Facebook group, tweet about it from the Beta Twitter, or send our a mass text through Remind101.</p>
  </div>

  <?php } // Show if recordset not empty ?>

</div>

<?php include('footer.php') 

?>
