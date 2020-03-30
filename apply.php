<?php require_once('Connections/nghsbeta.php'); ?>
<?php
define('INCLUDE_CHECK',true);

require_once('Connections/nghsbeta.php');
require 'functions.php';

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "officerApplication")) {
  $insertSQL = sprintf("INSERT INTO officerApplication (studentID, studentName, studentEmail, studentPhone, `position`, shortAnswer, teacherA, teacherB) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       $_SESSION['MM_Username'],
                       GetSQLValueString($_POST['studentName'], "text"),
                       GetSQLValueString($_POST['studentEmail'], "text"),
                       GetSQLValueString($_POST['studentPhone'], "text"),
                       GetSQLValueString($_POST['position'], "text"),
                       GetSQLValueString(implode('<br /><br /> ', $_POST['shortAnswer']), "text"),
                       GetSQLValueString(implode(', ', $_POST['teacherA']), "text"),
                       GetSQLValueString(implode(', ', $_POST['teacherB']), "text"));

  mysql_select_db($database_nghsbeta, $nghsbeta);
  $Result1 = mysql_query($insertSQL, $nghsbeta) or die(mysql_error());

	send_mail(	$_POST['studentEmail'],
		'support@nghsbeta.com',
		'Beta Officer Application: ' . $_POST['studentName'],
		'Name: <strong>' . $_POST['studentName'] . '</strong><br /> Student ID: ' . $_SESSION['MM_Username'] . '<br /> Email: '. $_POST['studentEmail'] . '<br /> Phone: ' . $_POST['studentPhone'] . '<br /> Teacher Recommendation Requests: ' . implode(', ', $_POST['teacherA']) . ' and ' . implode(', ', $_POST['teacherB']) . '<br /><br /> Position Running For: <strong>'. $_POST['position'] . '</strong><br /><br /><br />' . implode('<br /><br /> ', $_POST['shortAnswer']));

  $insertGoTo = "dashboard";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


$pageTitle = 'Apply';
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

    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Apply To Be An Officer</h1>
    <div class="info block">
    	<p><strong>Get involved with Beta Club!</strong> Apply to be an officer by filling out the application to the right. In addition to the application, an interview with the club sponsors and senior officiers, your two teacher recommendations, and the votes you receive by club members online will contribute to our decision of the 2015-2016 executive board.</p>
        <p><strong>This application is due March 20, 2015.</strong> Please sign-up for your interview by Friday, March 20 outside Mrs. Carlisle's room (#613). Interviews will take place Monday, March 23 to Friday April 3 during lunch and after school. Member voting will take place online in early April, and final decisions will be announced Monday, April 20.</p>
    </div>
    <div class="block">
    	<h2>Officer Application</h2>
  <form action="<?php echo $editFormAction; ?>" method="POST" name="officerApplication" class="fullForm">
        	<input name="studentID" type="text" readonly="true" value="<? echo $_SESSION['MM_Username'] ?>" style="background:none;"/><br />
        	<input name="studentName" type="text" readonly="true" value="<?php echo $row_memberInformation['fName'] . " " . $row_memberInformation['lName'] ?>" style="background:none;" /><br />
        	<input name="studentEmail" type="text" readonly="true" value="<?php echo $row_memberInformation['email'] ?>" style="background:none;" /><br />
        	<input name="studentPhone" type="text" readonly="true" value="<?php echo $row_memberInformation['phone'] ?>" style="background:none;" /><br />
        	<select name="position" id="position">
        		<option value="">Select Position Applying For</option>
        		<option value="President">President</option>
        		<option value="Service VP">Service VP</option>
        		<option value="Fundraising VP">Fundraising VP</option>
        		<option value="Meeting Coordinator">Meeting Coordinator</option>
        		<option value="Sophomore Level Grade Representative">Sophomore Level Grade Representative</option>
        		<option value="Junior Level Grade Representative">Junior Grade Level Representative</option>
        		<option value="Senior Level Grade Representative">Senior Grade Level Representative</option>
        		<option value="Technology Coordinator and Website Administrator">Technology Coordinator and Website Administrator</option>
        		<option value="Secretary of Membership">Secretary of Membership</option>
        		<option value="Secretary of Communications & Public Relations">Secretary of Communications & Public Relations</option>
        		<option value="Treasurer">Treasurer</option>
        		<option value="Historian">Historian</option>
        	</select><br /><br />
            <strong>Teacher Recommendations</strong> Please request two of your teachers to submit an online recommendation of you. Enter their names and email addresses below and they will receive an email shortly with a link to complete the online evaluation.<br /><br />
        	<input name="teacherA[]" type="text" value="Teacher #1 Name" onfocus="setValue(this)" onblur="setValue(this)" /><br />            
        	<input name="teacherA[]" type="text" value="Teacher #1 Email" onfocus="setValue(this)" onblur="setValue(this)" /><br />            
        	<input name="teacherB[]" type="text" value="Teacher #2 Name" onfocus="setValue(this)" onblur="setValue(this)" /><br />            
        	<input name="teacherB[]" type="text" value="Teacher #2 Email" onfocus="setValue(this)" onblur="setValue(this)" />
            <br /><br />
        	<strong>List your extracurricular activities in and out of NGHS. </strong>Indicate both the grades you were or plan to be actively involved with each activity along with any leadership positions you held or plan to hold.<br /><br />
        	<input name="shortAnswer[]" type="hidden" value="<strong>List your extracurricular activities in and out of NGHS. Indicate both the grades you were or plan to be actively involved with each activity along with any leadership positions you held or plan to hold.</strong>" />
            <textarea name="shortAnswer[]" id="shortAnswerA" cols="" rows="5"></textarea><br /><br />
        	<strong>Why do you want to be a Beta Club officer? </strong>What kind of leader are you (excite, examine, execute, empathy, explore, or any combination of the above)? What will you bring to the club as an officer?<br /><br />
            <input name="shortAnswer[]" type="hidden" value="<strong>Why do you want to be a Beta Club officer? What kind of leader are you (excite, examine, execute, empathy, explore, or any combination of the above)? What will you bring to the club as an officer?</strong>" />
        	<textarea name="shortAnswer[]" id="shortAnswerB" cols="" rows="5"></textarea><br /><br />
        	<strong>What is something you would like to accomplish as an officer (or officer team or club) in the upcoming year? </strong>If you have accomplished any significant or interesting project in the past (as a club officer or leader in a group), elaborate on its goals, what you were able to do, and how you contributed to the project.<br /><br />
            <input name="shortAnswer[]" type="hidden" value="<strong>What is something you would like to accomplish as an officer (or officer team or club) in the upcoming year? If you have accomplished any significant or interesting project in the past (as a club officer or leader in a group), elaborate on its goals, what you were able to do, and how you contributed to the project.</strong>" />
        	<textarea name="shortAnswer[]" id="shortAnswerC" cols="" rows="5"></textarea><br /><br />
        	<strong>Discuss your favorite and least favorite Beta events or projects and why these events were or were not memorable. </strong>Are there any projects from this past year you would like to lead next year? What are some improvements that you see need to be made? Do you have any new project ideas you would like to initiate next year?<br /><br />
            <input name="shortAnswer[]" type="hidden" value="<strong>Discuss your favorite and least favorite Beta events or projects and why these events were or were not memorable. Are there any projects from this past year you would like to lead next year? What are some improvements that you see need to be made? Do you have any new project ideas you would like to initiate next year?</strong>" />
        	<textarea name="shortAnswer[]" id="shortAnswerD" cols="" rows="5"></textarea><br /><br />
            <br />
            Please read the following. Ensure you understand what is expected of you as a Beta Club officer. With your and your parent's signatures, you are committing to fulfilling these team expectations.
            <ul style="text-align:left;">
            	<li>Beta Club offices require dedication, responsibility, hard work, along with weekend and after school time. As an officer, you will be expected to lead at least 1-2 events per semester as well as assist other officers. Officer meetings are weekly after school.</li>
                <li>You will have to provide transportation to and from all morning and afternoon meetings and activities. Monthly meetings start at 6:30a and after school meetings sometimes go until 3:00p.</li>
                <li>You must act as a positive role model both inside and outside school, including on social media. Refrain from posting about teachers, participating in any illegal activities, and most importantly degrading or bullying other students.</li>
            </ul>
        	<input name="studentSignature" type="text" value="Student Electronic Signature" onfocus="setValue(this)" onblur="setValue(this)" style="font-size:16px;" /><br />            
        	<input name="parentSignature" type="text" value="Parent or Guardian Electronic Signature" onfocus="setValue(this)" onblur="setValue(this)" style="font-size:16px;" /><br /><br />
            <input name="submitApp" value="Submit Application" type="submit" style="width: 100%;padding: 15px;background: #F03204;" />
            <input type="hidden" name="MM_insert" value="officerApplication" />
  </form>
  </div>
    <a href="http://goo.gl/P2kDsr" target="_blank"><div class="info block">
    	<h2>Officer Positions</h2>
  <ul style="text-align:left;">
        	<li>President</li>
        	<li>Service VP</li>
        	<li>Fundraising VP</li>
            <li>Meeting Coordinator</li>
        	<li>Grade Level Representative</li>
        	<li>Technology Coordinator and Website Administrator</li>
        	<li>Secretary of Membership</li>
        	<li>Secretary of Communications & Public Relations</li>
        	<li>Treasurer</li>
        	<li>Historian</li>
      </ul>
        <p>Click here for a detailed description of each officer position along with the tasks and responsibilities of each.</p>
    </div></a>
</div>

<?php include('footer.php') ?>