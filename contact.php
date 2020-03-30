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

$colname_officerEmail = "-1";
if (isset($_GET['id'])) {
  $colname_officerEmail = $_GET['id'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_officerEmail = sprintf("SELECT email, fName, lName, `role` FROM beta_members WHERE id = %s", GetSQLValueString($colname_officerEmail, "int"));
$officerEmail = mysql_query($query_officerEmail, $nghsbeta) or die(mysql_error());
$row_officerEmail = mysql_fetch_assoc($officerEmail);
$totalRows_officerEmail = mysql_num_rows($officerEmail);

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_officers = "SELECT id, email, fName, lName FROM beta_members WHERE `role` = 'officer' ORDER BY `year` ASC";
$officers = mysql_query($query_officers, $nghsbeta) or die(mysql_error());
$row_officers = mysql_fetch_assoc($officers);
$totalRows_officers = mysql_num_rows($officers);


define('INCLUDE_CHECK',true);

require 'functions.php';

	$pageTitle = 'Contact';
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
    </script>

    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
        <?php if (($totalRows_officerEmail > 0)&&($row_officerEmail['role']=="officer")) {
			$toEmail = $row_officerEmail['email'];
			?>
	<h1>Contact <strong><?php echo $row_officerEmail['fName'] ?></strong></h1>
		<?php }
		else {
			$toEmail = "nghs.betaclub@gmail.com";
			echo "<h1>Contact Us</h1>";
		}
		?>
    <div class="block">
    	<? if (!isset($_GET['id'])) { ?><p>Please fill out the contact form below, and we'll get back to you as soon as we can!</p><? } ?>

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
	$to = $toEmail;
    $name = $_REQUEST['name'] ;
    $email = $_REQUEST['email'] ;
    $subject = $_REQUEST['subject'] . " [NG Beta Contact Form]";
	$message = "<html><body>";
    $message .= $_REQUEST['message'] ;
	$message .= "<br /><br />$name";
	$message .= "</body></html>";
	$headers = '';
	$headers .= "From: $email\n";
	$headers .= "Reply-to: $email\n";
	$headers .= "Return-Path: $email\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($to,$subject,$message,$headers);
    echo "<p style='color:green;'>Thank you for contacting us! Your message has been delievered. A Beta representative will be in contact with you shortly.</p>";
    }
  }
else
  {//if "email" is not filled out, display the form
  echo "<form action='$editFormAction' method='POST' name='contact' class='fullForm'>
        	<input name='name' type='text' maxlength='50' value='Name' onfocus='setValue(this)' onblur='setValue(this)' /><br />
        	<input name='email' type='text' maxlength='50' value='Email' onfocus='setValue(this)' onblur='setValue(this)' /><br />
            <input name='subject' type='text' onfocus='setValue(this)' onblur='setValue(this)' value='Subject' maxlength='150' /><br />
            <textarea name='message' cols='' rows='10' onfocus='setValue(this)' onblur='setValue(this)'>Message</textarea><br />
            <input name='contact' type='submit' value='Submit' style='width: 100%;padding: 15px;background: #F03204;' />
            <input type='hidden' name='MM_insert' value='contact' />
  </form>";
  }
  ?>    	
  </div>
  <!--<div class="info block">
  	<h2>Email An Officer</h2>
    <?php do { ?>
      <a href="contact?id=<?php echo $row_officers['id']; ?>"><div class="darkBtn button <?php if ($row_officers['id']==$_GET['id']){ echo "active"; }?>"><?php echo $row_officers['fName']; ?> <?php echo $row_officers['lName']; ?></div></a><br />
      <?php } while ($row_officers = mysql_fetch_assoc($officers)); ?>
  </div>-->
</div>
<?php
$colname_officerEmail = "-1";
if (isset($_GET['id'])) {
  $colname_officerEmail = $_GET['id'];
}
$officer_officerEmail = "officer";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "contact") && ($mailcheck==TRUE)) {
  $insertSQL = sprintf("INSERT INTO contactForm (emailTo, emailFrom, messageSubject, messageContent, personFromName) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($toEmail, "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['message'], "text"),
                       GetSQLValueString($_POST['name'], "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

}


include('footer.php') 

?>
