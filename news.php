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

$maxRows_news = 10;
$pageNum_news = 0;
if (isset($_GET['pageNum_news'])) {
  $pageNum_news = $_GET['pageNum_news'];
}
$startRow_news = $pageNum_news * $maxRows_news;

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_news = "SELECT articleID, articleTitle, articleTagline, articleAuthor, articleDate FROM news ORDER BY articleDate DESC";
$query_limit_news = sprintf("%s LIMIT %d, %d", $query_news, $startRow_news, $maxRows_news);
$news = mysql_query($query_limit_news, $nghsbeta) or die(mysql_error());
$row_news = mysql_fetch_assoc($news);

if (isset($_GET['totalRows_news'])) {
  $totalRows_news = $_GET['totalRows_news'];
} else {
  $all_news = mysql_query($query_news);
  $totalRows_news = mysql_num_rows($all_news);
}
$totalPages_news = ceil($totalRows_news/$maxRows_news)-1;
 
	$pageTitle = 'News';
	include('header.php')
	?>
    
<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<h1>Beta News</h1>
    <?php do { ?>
      <a href="article?articleID=<?php echo $row_news['articleID']; ?>"><div class="block">
        <h2><?php echo $row_news['articleTitle']; ?></h2>
        <p><?php echo $row_news['articleTagline']; ?></p>
        <div class="button">Read More</div>
      </div></a>
      <?php } while ($row_news = mysql_fetch_assoc($news)); ?>
</div>

<?php include('footer.php') 
?>
<?php
mysql_free_result($news);
?>
