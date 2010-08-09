<?php
$password = "sanjuan";

echo $password;
/* displays secret */
echo "<br>";
$password = sha1($password);

echo $password; 
/* displays e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4 */
?>