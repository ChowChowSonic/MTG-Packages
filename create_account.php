<?php include_once('header.php'); 
?>
<form action="account_confirm.php" method="post" style="border: 2px solid white; width:50%; text-align:center; margin-left: 25%; padding-bottom: 2%;">
	<h2>Create account:</h2>
	<input type="text" name="nam" placeholder="Username"/><br>
	<input type="password" name="pw" placeholder="Password"/><br>
	<input type="submit"/><br>
</form>
<?php include_once("footer.php")?>