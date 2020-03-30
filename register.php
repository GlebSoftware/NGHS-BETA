<?php require_once('Connections/nghsbeta.php'); ?>
<?php header("Location:../login");
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

$colname_invites = "-1";
if (isset($_POST['studentID'])) {
  $colname_invites = $_POST['studentID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_invites = sprintf("SELECT * FROM invites WHERE studID = %s", GetSQLValueString($colname_invites, "int"));
$invites = mysql_query($query_invites, $nghsbeta) or die(mysql_error());
$row_invites = mysql_fetch_assoc($invites);
$totalRows_invites = mysql_num_rows($invites);

define('INCLUDE_CHECK',true);

require_once('Connections/nghsbeta.php');
require 'functions.php';

session_name('register');
// Starting the session

session_set_cookie_params(60*60);
// Making the cookie live for an hour

session_start();

if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
	
	$_POST['studentID'] = mysql_real_escape_string($_POST['studentID']);
		
		// Escaping all input data



if ($totalRows_invites > 0) { // Show if recordset not empty

	if(!checkEmail($_POST['email']))
	{
		$err[]='Your email is not valid!';
	}
	
	if(strlen($_POST['grad'])<4 || strlen($_POST['grad'])>4)
	{
		$err[]='Your graduation year should be 4 digits.';
	}

	if(strlen($_POST['lunch'])>1)
	{
		$err[]='Please select your lunch period.';
	}	

	if(strlen($_POST['tshirt'])>2)
	{
		$err[]='Please select your t-shirt size.';
	}	

	if(strlen($_POST['phone'])<10 || strlen($_POST['phone'])>10)
	{
		$err[]='Your phone number should be 10 digits (type all 0\'s if you don\'t have one).';
	}

	if(!count($err))
	{
		// If there are no errors
				
		$_POST['email'] = mysql_real_escape_string($_POST['email']);
		$_POST['studentID'] = mysql_real_escape_string($_POST['studentID']);
		$_POST['fName'] = mysql_real_escape_string($_POST['fName']);
		$_POST['lName'] = mysql_real_escape_string($_POST['lName']);
		$_POST['grad'] = mysql_real_escape_string($_POST['grad']);
		$_POST['phone'] = mysql_real_escape_string($_POST['phone']);
		$_POST['lunch'] = mysql_real_escape_string($_POST['lunch']);
		$_POST['tshirt'] = mysql_real_escape_string($_POST['tshirt']);
		// Escape the input data
		
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "register")) {
  $insertSQL = sprintf("INSERT INTO beta_members (email, stuID, fName, lName, `year`, phone, tshirt, lunch, regIP) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['studentID'], "int"),
                       GetSQLValueString($_POST['fName'], "text"),
                       GetSQLValueString($_POST['lName'], "text"),
                       GetSQLValueString($_POST['grad'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['tshirt'], "text"),
                       GetSQLValueString($_POST['lunch'], "text"),
                       GetSQLValueString($_SERVER['REMOTE_ADDR'], "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

			send_mail(	'membership@nghsbeta.com',
						$_POST['email'],
						'North Gwinnett Beta Club',
						'Congratulations! You are now a NG Beta Club Member! To access your points, sign-in on the website, and volunteer for events, use your student ID to login at nghsbeta.com/login.');
			
			$_SESSION['msg']['reg-success']='Congratulations! You are now a NG Beta Club Member. We\'ve sent you a confirmation email.';
			
			header("Location: login");
			
}
		else $err[]='This student ID has already signed up.';
	}
}
else $err[]='The student ID you entered has not been invited into the Beta Club. Please make sure you entered your student ID correctly.';

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	

	
}

?>

<?php
	$pageTitle = 'Register';
	$noMenu = true;
	include('header.php')
	?>
    
<div class="main" style="position:absolute; height:100%;background-image:url('<?php echo $imageSelected; ?>'); padding:0;">

	    <?php
              if($_SESSION['msg']['reg-err'])
              {
                  echo '<div class="message" style="background: darkred;">'.$_SESSION['msg']['reg-err'].'</div>';
                  unset($_SESSION['msg']['reg-err']);
              }
              
              if($_SESSION['msg']['reg-success'])
              {
                  echo '<div class="message" style="background: darkgreen;>'.$_SESSION['msg']['reg-success'].'</div>';
                  unset($_SESSION['msg']['reg-success']);
              }
          ?>

	<div class="center block" style="margin-top:-225px;">
  <h1>Join <strong>Beta</strong></h1>
<form action="<?php echo $editFormAction; ?>" method="POST" name="register">
  <label for="studentID">Student ID:</label>
          <input type="text" name="studentID" id="studentID" value=""/><br/>
          <label for="fName">First Name:</label>
          <input type="text" name="fName" id="fName" /><br/>
          <label for="lName">Last Name:</label>
          <input type="text" name="lName" id="lName" /><br/>
          <label for="grad">Graduation Year:</label>
          <input type="text" name="grad" id="grad" /><br/>
          <label for="lunch">Lunch Period:</label>
          <select name="lunch" id="lunch"><option value="lunch">Select Period</option><option value="4">4th</option><option value="5">5th</option><option value="6">6th</option></select><br/>
          <label for="phone">Cell Phone:</label>
          <input type="text" name="phone" id="phone" value="0000000000"/><br/>
          <label for="email">Email:</label>
          <input type="text" name="email" id="email" /><br/>
          <label for="tshirt">T-Shirt Size:</label>
          <select name="tshirt" id="tshirt"><option value="tshirt">Select Size</option><option value="s">Small</option><option value="m">Medium</option><option value="l">Large</option><option value="xl">Extra Large</option></select><br/>
          <input type="submit" name="submit" value="Register" style="width: 100%;padding: 15px;background: #F03204;" />
          <input type="hidden" name="MM_insert" value="register" />
</form>
    </div>
</div>

</body>
</html><?php
mysql_free_result($invites);
?>
