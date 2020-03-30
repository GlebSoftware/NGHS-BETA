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

$colname_news = "-1";
if (isset($_GET['articleID'])) {
  $colname_news = $_GET['articleID'];
}
mysql_select_db($database_nghsbeta, $nghsbeta);
$query_news = sprintf("SELECT * FROM news WHERE articleID = %s", GetSQLValueString($colname_news, "int"));
$news = mysql_query($query_news, $nghsbeta) or die(mysql_error());
$row_news = mysql_fetch_assoc($news);
$totalRows_news = mysql_num_rows($news);
 
	$pageTitle = $row_news['articleTitle'];
	include('header.php')
	?>

<div id="fb-root"></div>


<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
  <div class="content block">
  	<div class="fb-like" data-href="http://www.nghsbeta.com/article?articleID=<?php echo $row_news['articleID']; ?>" data-layout="box_count" data-colorscheme="dark" data-action="like" data-show-faces="true" data-share="false" style="float:right;"></div>
  	<h1 style="font-size: 50px; display: inline-block; margin-right: 15px;"><?php echo $row_news['articleTitle']; ?></h1>
    <span><?php echo $row_news['articleAuthor']; ?></span>
    <p><?php echo $row_news['articleTagline']; ?></p>
    <p><?php echo $row_news['article']; ?></p>
  </div>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($news);
?>
