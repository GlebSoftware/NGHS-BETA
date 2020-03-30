<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_nghsbeta = "localhost";
$database_nghsbeta = "nghsbeta_members";
$username_nghsbeta = "nghsbeta";
$password_nghsbeta = "beta2support";
$nghsbeta = mysql_pconnect($hostname_nghsbeta, $username_nghsbeta, $password_nghsbeta) or trigger_error(mysql_error(),E_USER_ERROR); 
?>