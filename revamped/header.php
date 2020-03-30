<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
<meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<script src="js/twitter.js"></script>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
<title><? echo $pageTitle; ?> | North Gwinnett Beta Club</title>
</head>

<body>
<div class="social">
	<a href="http://www.facebook.com/groups/157195107695902/" title="NG Beta Facebook Group" target="_blank"><div style="background:#3B5998;">f</div></a>
	<a href="https://twitter.com/nghsbeta" title="NG Beta Twitter Page" target="_blank"><div style="background:#4099FF;">t</div></a>
</div>
<div class="navBar topBar">
    <ul>
        <li><a href="news.php" title="News">News</a></li>
        <li><a href="plan.php" title="Plan An Event">Plan</a></li>
        <li><a href="volunteer.php" title="Volunteer for Events">Volunteer</a></li>
        <li class="active"><a href="index.php" title="Dashboard"><?php echo $row_memberInformation['fName']; ?></a></li>
        <li><a href="media.php" title="Submit a Video">Video</a></li>
        <li><a href="about.php" title="About">About</a></li>
        <li><a href="contact.php" title="Contact">Contact</a></li>
    </ul>
</div>
<?php if ($row_memberInformation['role']=="officer") { // Show if officer ?>
  <div class="navBar bottomBar">
    <ul>
      <li>News</li>
      <li>Notes</li>
      <li>Events</li>
      <li><strong>Officer</strong></li>
      <li><a href="officer/addEvent.php" title="New Event">New</a></li>
      <li>Meeting</li>
      <li><a href="officer/members.php" title="Beta Members">Members</a></li>
      </ul>
  </div>
<?php } ?>