<?php 
if($_GET['valid'] == "false"){
  echo "<span style=\"color:#F00\">Incorrect username or password</span>";
}
?>
<form action="validate.php" method="post">
  <label for="username">ID</label>
  <input type="text" name="username" id="username" />
  <br />
  <label for="password">Password</label>
  <input type="password" name="password" id="password" />
  <br />
  <input type="submit" name="submit_login" value="Log in" />
  <?php if(isset($_GET['dest'])){
  echo "<input type=\"hidden\" name=\"dest\" value=\"" . $_GET['dest'] . "\" />";
  }
  ?>
</form>