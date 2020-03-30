<?php require_once('Connections/nghsbeta.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="The North Gwinnett Beta Club empowers students to &quot;lead by serving others,&quot; through community service projects and fundraisers. ">
    <meta name="keywords" content="North Gwinnett Service Clubs Leadership Community Service Achievement Beta">
    <meta name="viewport" content="width=device-width" />
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
    <title>North Gwinnett Beta Club</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script src="js/twitter.js"></script>
	<script src="js/modernizr.custom.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic' rel='stylesheet' type='text/css'>
  </head>
<body>

<?php include('imageSelector.php') ?>

<div class="main" style="position:absolute; height:100%;background-image:url('<?php echo $imageSelected; ?>');">
	<div class="center block">
    	<h1>Need <strong>Help?</strong></h1>
        <p>Having trouble signing in? Please enter your student ID below:</p>
        <form ACTION="" method="POST" name="help" class="login">
            <input name="studentID" class="input" type="text" value="Student ID" maxlength="10" onfocus="setValue(this)" onblur="setValue(this)" />
            <input name="submit" type="submit" value="Login" style="background: #F03204;"/>
        </form>
    </div>
</div>

</body>
</html>
