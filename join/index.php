<?php

define('INCLUDE_CHECK',true);

require 'connect.php';
require 'functions.php';
// Those two files can be included only if INCLUDE_CHECK is defined


session_name('register');
// Starting the session

session_set_cookie_params(24*60*60);
// Making the cookie live for a day

session_start();

if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();
	
	// Destroy the session
}


if(isset($_GET['logoff']))
{
	$_SESSION = array();
	session_destroy();
	
	header("Location: index.php");
	exit;
}

if($_POST['submit']=='Register')
{
	// If the Register form has been submitted
	
	$err = array();
	
	$_POST['studentID'] = mysql_real_escape_string($_POST['studentID']);
		
		// Escaping all input data

$row = mysql_fetch_assoc(mysql_query("SELECT studID FROM invites WHERE studID='{$_POST['studentID']}'"));

if($row)
{

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

	if(strlen($_POST['dues'])=="paid")
	{
		$err[]='Please pay your club dues.';
	}	

	if(strlen($_POST['tshirt'])>2)
	{
		$err[]='Please select your t-shirt size.';
	}	

	if(strlen($_POST['phone'])<10 || strlen($_POST['phone'])>10)
	{
		$err[]='Your phone number should be 10 digits (type all 0\'s if you don\'t have one).';
	}

	if(strlen($_POST['forms'])=="filled")
	{
		$err[]='Please fill out both forms and turn them in as soon as possible.';
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
		
		
		mysql_query("	INSERT INTO beta_members(email,stuID,fName,lName,year,phone,tshirt,lunch,regIP,dt)
						VALUES(
						
							'".$_POST['email']."',
							'".$_POST['studentID']."',
							'".$_POST['fName']."',
							'".$_POST['lName']."',
							'".$_POST['grad']."',
							'".$_POST['phone']."',
							'".$_POST['tshirt']."',
							'".$_POST['lunch']."',
							'".$_SERVER['REMOTE_ADDR']."',
							NOW()
							
						)");
		
		if(mysql_affected_rows($link)==1)
		{
			send_mail(	'membership@nghsbeta.com',
						$_POST['email'],
						'North Gwinnett Beta Club',
						'Congratulations! You are now a NG Beta Club Member! To access your points, sign-in on the website, and volunteer for events, use your student ID to login at members.nghsbeta.com. We are very excited to share with you all the cool new initiatives and project ideas for Beta 2015.');
			
			header("Location: registered.html");
			
			$_SESSION['msg']['reg-success']='Congratulations! You are now a NG Beta Club Member. You should have received a confirmation email.';
		}
		else $err[]='This student ID has already signed up.';
	}
}
else $err[]='The student ID you entered has not been invited into the Beta Club.<br/>Please make sure you entered your student ID in correctly.';

	if(count($err))
	{
		$_SESSION['msg']['reg-err'] = implode('<br />',$err);
	}	

	
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Join | North Gwinnett Beta Club</title>
<link rel='shortcut icon' href='../favicon.ico' type='image/x-icon'/ >
<link rel="stylesheet" type="text/css" media="all" href="joinStyle.css" />
<link href='http://fonts.googleapis.com/css?family=Coustard|Chau+Philomene+One|Lobster' rel='stylesheet' type='text/css'>
</head>
<body>
   <div class="background" id="topleft"></div>
   <div class="background" id="topright"></div>
   <div class="background" id="bottomleft"></div>
   <div class="background" id="bottomright"></div>
<div class="main">
<div class="topBar"><span class="header">Beta</span><br/>
  <span class="subtitle">Membership</span></div>
  <!--<p class="highlight" style="background:#C06362">Registration for Beta 2013 have <strong>NOT</strong> begin yet. If you have received your invitation, please wait until registration starts. Thank you for your patience!</p>-->
  <a style="text-decoration: none;" href="http://goo.gl/NuDfwJ" target="_blank"><p class="highlight" style="background:#3F5758">Congratulations on being invited into the largest service and leadership organization on campus. The NG Beta Club is made up of the best and brightest students North Gwinnett has to offer. To join the Beta Club, follow the directions below. <strong>Click here to view the invitation.</strong></p></a>
<!--  <p><strong>Congratulations!</strong> The North Gwinnett Beta Club is the largest student organization on campus, made up of the best and brightest students North Gwinnett has to offer. Membership is based on student's cumulative grade percent average. To be a member of the Beta Club, students must attain and maintain an "A" average.</p>
  <p>In past years, NGHS Beta Alums have been granted acceptance to the nation's most prestigious universities: Duke, Harvard, Princeton, Yale, Stanford, MIT, University of North Caroline, Florida State, University of Miami, Emory, Brown, and of course Georgia Tech and the University of Georgia (just to name a few).</p>
  <p>Beta members care about their school, local community, and the global community. Each year Beta participates in various service projects ranging from the annual "Haunted House" event, to competing in Convention, being involved in Beta Week, and to raising money for local charities with our annual Car Washes. Beta members make a huge difference in their community! Each year, the NG Beta Club alone raises thousands of dollars to support a multitude of different organizations, and our members total over 15,000 recorded community service hours each year.</p>
  <p>At the end of the year, Beta members are responsible for having at least 16 community service hours, 8 of which must be "beta sponsored" activities (including any activity listed on our website).</p>
  <p>Beta gives students the opportunity to build leadership skills. In additional to the executive council, many projects require team captains to help with planning and execution stages of service projects. Also, many scholarships are offered through National Beta Club, and last year, few of our very own students won! Finally, at graduation, members will walk with honor with a cord representing their service in Beta Club.</p>-->
  <p class="highlight" style="background:#368C6F;">Please fill out your information into the form below and pay the club dues on MyPaymentsPlus to accept your invitation into The Beta Club. Once your account has been created, you may use your student ID to log-in to the Beta site, volunteer for events, and access your points. Please email any questions to <strong>membership@nghsbeta.com</strong></p>
  <!-- Register Form -->
  <form action="" method="post">
                    <?php
						
						if($_SESSION['msg']['reg-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
							unset($_SESSION['msg']['reg-err']);
						}
						
						if($_SESSION['msg']['reg-success'])
						{
							echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
							unset($_SESSION['msg']['reg-success']);
						}
					?>
<br/>
	<span class="formInputs">
    <label class="grey" for="studentID">Student ID:</label>
    <input class="field" type="text" name="studentID" id="studentID" value=""/><br/>
    <label class="grey" for="fName">First Name:</label>
    <input class="field" type="text" name="fName" id="fName" /><br/>
    <label class="grey" for="lName">Last Name:</label>
    <input class="field" type="text" name="lName" id="lName" /><br/>
    <label class="grey" for="grad">Graduation Year:</label>
    <input class="field" type="text" name="grad" id="grad" /><br/>
    <label class="grey" for="lunch">Lunch Period:</label>
    <select class="fieldDrop" name="lunch" id="lunch"><option value="lunch">Select Period</option><option value="4">4th</option><option value="5">5th</option><option value="6">6th</option></select><br/>
    <label class="grey" for="phone">Cell Phone:</label>
    <input class="field" type="text" name="phone" id="phone" value="0000000000"/><br/>
    <label class="grey" for="email">Email:</label>
    <input class="field" type="text" name="email" id="email" /><br/>
    <p class="highlight" style="background:#BEA784;">Beta Dues</p>
    <p>
    Your Beta dues will help pay for your club t-shirt, snacks at meetings, state convention, and all the fundraisers we organize and hold. Please pay club dues at MyPaymentsPlus <strong>right now</strong> to ensure your membership into the NG Beta Club.
    <input class="checkbox" type="checkbox" name="dues" id="dues" value="paid">
    <label class="grey" for="dues">I have paid my Beta dues</label>
    </p>
    <label class="grey" for="tshirt">T-Shirt Size:</label>
    <select class="fieldDrop" name="tshirt" id="tshirt"><option value="tshirt">Select Size</option><option value="s">Small</option><option value="m">Medium</option><option value="l">Large</option><option value="xl">Extra Large</option></select><br/>
    <p class="highlight" style="background:#C06362;">Remind101</p>
    <p>Because we are such a large club, communication is super important. We try our best to give you information in a variety of ways, including our website (our most important means of communication), Facebook, Twitter, email, and now texts. To subscribe to our Remind101 and receive text message updates to your phone, please text "@nghsbeta" to (706) 813-4236.</p>    
    <a style="text-decoration: none;" href="http://www.facebook.com/groups/157195107695902/" target="_blank"><p class="highlight" style="background:#3B5998;">Join Our Facebook Group</p></a>
    <p>On Facebook, random members a part of the official NGHS Beta Club Facebook Group that interact with the group often (ex. liking posts) will be eligible to win prizes. The social media giveaways will be given out each meeting!</p>
    <a style="text-decoration: none;" href="https://twitter.com/nghsbeta" target="_blank"><p class="highlight" style="background:#4099FF;">Follow Us On Twitter</p></a>
    <p>On Twitter, random members who retweet selective tweets by the official NGHS Beta Twitter will be eligible to win fabulous prizes (must be a member of beta club and following the official NGHS Beta Twitter).  The social media giveaways will be given out each meeting!</p>
    <a style="text-decoration:none;" href="http://www.nghsbeta.com/resources/alternative_transportation_liability_form.pdf"><p class="highlight" style="background:#BEA784;">Transportation Liability Form</p></a>
    <p>
    Please fill out the Transportation Liability Form your received with your invitation (or click on the link above) and return it to Mrs. Carlisle's room (#613), Mrs. Pinkerton's room (#904), or Mr. Youmans room (#201) as soon as possible. <strong>The directions on your invitation mentioned two forms. The media release form was taken care of on MyPaymentsPlus and you therefore do NOT need to worry about it.</strong> If you lost this form, you can download a copy <a href="http://www.nghsbeta.com/resources/alternative_transportation_liability_form.pdf">here</a>.<br/>
    <input class="checkbox" type="checkbox" name="forms" id="forms" value="filled">
    <label class="grey" for="forms">I have filled out the Transportation Liability Form.</label>
    </p>
    </span>
    <input type="submit" name="submit" value="Register" class="bt_register" />
  </form>
</div>
</body>
</html>
