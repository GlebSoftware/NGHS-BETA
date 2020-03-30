<?php


function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($consumerkey, $consumersecret, $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$consumerkey = "8nL9RlmFfrekMPTV7NFzA";
$consumersecret = "0oomWjiZXVJsgaUf6SbNhmMh2w6M0kLOlH6Kdmc5Tw";
$accesstoken = "386173871-x6yRv4mOY62Ya71PjUnRiIn6d4Yc2x0t9r2nA6jx";
$accesstokensecret = "blB9iVUlpchfeaI2RV9m2seRFBvadnycwFu1oEfihkGaQ";

$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
$content = $connection->get("statuses/home_timeline");

	$pageTitle = 'Post To Twitter';
	include('header.php')
 ?>


<div class="main" style="background-image:url('<?php echo $imageSelected; ?>');">
	<div class="block">
    </div>	
</div>

<?php include('footer.php') 

?>