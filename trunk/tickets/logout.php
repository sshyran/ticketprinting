<?php
session_start();
function LogOut() {
  
     
    $_SESSION["access"] = "denied";
      
}
LogOut();
?>
<html>
<head>
</head>
<body>
<p>You are now logged out.</p>
<p><a href="login.php">Log in</a></p>
</body></html>