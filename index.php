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

mysql_select_db($database_nghsbeta, $nghsbeta);
$query_recentArticle = "SELECT articleID, articleTitle, articleTagline, articleDate FROM news ORDER BY articleDate DESC LIMIT 2";
$recentArticle = mysql_query($query_recentArticle, $nghsbeta) or die(mysql_error());
$row_recentArticle = mysql_fetch_assoc($recentArticle);
$totalRows_recentArticle = mysql_num_rows($recentArticle);
 
 
	$pageTitle = 'Home';
	include('header.php')
	?>
    
    <script>
	$(document).ready(function(){
		var $window = $(window); //You forgot this line in the above example
	
		$('div[data-type="background"]').each(function(){
			var $bgobj = $(this); // assigning the object
			$(window).scroll(function() {
				var yPos = -($window.scrollTop() / $bgobj.data('speed'));
				// Put together our final background position
				var coords = '50% '+ yPos + 'px';
				// Move the background
				$bgobj.css({ backgroundPosition: coords });
			});
		});
	});
	</script>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-146440450-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-146440450-1');
</script>

    
<div class="main" data-type="background" data-speed="5" style="background-image:url('<?php echo $imageSelected; ?>');">
    <h1>Igniting A Passion For Service</h1>
	<div class="fold">
    	<div class="title block">
            <a href="article?articleID=<?php echo $row_recentArticle['articleID']; ?>"><div class="button">Learn More</div></a>
	    	<h1><?php echo $row_recentArticle['articleTitle']; ?></h1>
        	<p><?php echo $row_recentArticle['articleTagline']; ?></p>
        </div>
        
        <!--<div class="title block">
        	<h1>BETA CAMPFIRE</h1>
        	<p>This Friday(9/11) at 2:30 in the theater</p>
        	<div class="imgContainer">
        		<img src="/img/campfire2015.png">
       		</div>
        </div>-->
        
    </div>
    <div id="whyilovebeta"></div>
</div>

<?php include('footer.php') 
?>
