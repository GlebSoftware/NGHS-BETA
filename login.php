<?php require_once('Connections/nghsbeta.php'); 

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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "role";
  $MM_redirectLoginSuccess = "dashboard";
  $MM_redirectLoginFailed = "login?error";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_nghsbeta, $nghsbeta);
  	
  $LoginRS__query=sprintf("SELECT stuID, email, role FROM beta_members WHERE stuID=%s AND username=%s",
  GetSQLValueString($loginUsername, "int"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $nghsbeta) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'role');
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}

	$pageTitle = 'Login';
	$noMenu = true;
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
    </script>

<?php include('imageSelector.php') ?>

<div class="main" style="position:absolute; height:100%;background-image:url('<?php echo $imageSelected; ?>'); padding:0;">
	<? if (isset($_GET['error'])) { ?>
	<a href="register" style="color:#FFF;"><div class="message" style="background: darkred;">Sorry! Login failed. <strong>Need help?</strong></div></a>
    <? } ?>
	<? if (isset($_GET['registered'])) { ?>
	<div class="message" style="background: darkgreen;">Congratulations! You are now a NG Beta Club Member. We've sent you a confirmation email.</div>
    <? } ?>    
	<div class="center block" style="margin-top: 155px;"><strong>Username: First name + Last initial + Last 3 digits of Student Number</strong>
	<p>If your account doesn't work, please <a href="mailto:alikhvergleb@gmail.com"> click here</a> to email Gleb, the sitemaster.</p></div>
	<div class="center block">
    	<h1>NG <strong>Beta</strong></h1>
        <form ACTION="<?php echo $loginFormAction; ?>" method="POST" name="login" class="login">
        	<input name="password" class="input" type="text" value="Username" onfocus="setValue(this)" onblur="setValue(this)" />
            <input name="username" class="input" type="password" maxlength="10" onfocus="setValue(this)" onblur="setValue(this)" placeholder="Student ID" />
            <input name="submit" type="submit" value="Login" style="background: #F03204;"/>
        </form><br /><br />
    </div>
</div>

</body>
</html>
