<?php require_once('Connections/nghsbeta.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
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

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_betaMembers = "SELECT stuID, fName, lName FROM beta_members ORDER BY fName ASC";
$betaMembers = mysql_query($query_betaMembers, $nghsbeta) or die(mysql_error());
$row_betaMembers = mysql_fetch_assoc($betaMembers);
$totalRows_betaMembers = mysql_num_rows($betaMembers);

 do { 
  $name[]=$row_betaMembers['fName'] . " " . $row_betaMembers['lName'];
  $fName[]=$row_betaMembers['fName'];
  $lName[]=$row_betaMembers['lName'];
  $sID[]=$row_betaMembers['stuID'];
 } while ($row_betaMembers = mysql_fetch_assoc($betaMembers)); 
 



//get the q parameter from URL
$q=$_GET["q"];
$url=$_GET["url"] . "?studentID=";

//lookup all hints from array if length of q>0
if (strlen($q) > 0)
  {
  $hint="";
  for($i=0; $i<count($fName); $i++)
    {
    if (((strtolower($q)==strtolower(substr($name[$i],0,strlen($q)))) || (strtolower($q)==strtolower(substr($lName[$i],0,strlen($q))))) || ((strlen($q)>5) && (strtolower($q)==strtolower(substr($sID[$i],0,strlen($q))))))
      {
      if ($hint=="")
        {
        $hint= "<a href='" . $url . $sID[$i] . "'><div class='button'>" . $name[$i] . "</div></a>";
        }
      else
        {
        $hint.= "<a href='" . $url . $sID[$i] . "'><div class='button'>" . $name[$i] . "</div></a>";
        }
      }
    }
  }

// Set output to "no suggestion" if no hint were found
// or to the correct values
if ($hint == "")
  {
  $response="(No Matching Names)";
  }
else
  {
  $response=$hint;
  }

//output the response
echo $response;






?>
