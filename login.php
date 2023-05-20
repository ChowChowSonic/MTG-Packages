<?php include_once('header.php');

if (key_exists("pw", $_POST) && key_exists("nam", $_POST)) {
	$sql = "SELECT user_id, username, password_hash from `accounts` WHERE username = :x;";
	$usr = $db->prepare($sql); //"SELECT (`emailAddress`, `password`, `fName`, `lName`) from `jewelryManagers` WHERE `emailAddress` == \"$_POST['pw']\""
	$usr->bindValue("x", sanitize($_POST['nam']), PDO::PARAM_STR); 
	$usr->execute();
	$usr = $usr->fetch();

	if ($usr != false && password_verify(sanitize($_POST["pw"]), $usr["password_hash"])) {
		$_SESSION["isLoggedIn"] = true;
		$_SESSION["user"] = $usr['username'];
		$_SESSION["id"] = $usr['user_id'];
	} else echo "Password was incorrect, please try again.";
}

if ($_SESSION["isLoggedIn"] == false) { ?>
	<form action="login.php" method="post" style="border: 2px solid white; width:50%; text-align:center; margin-left: 25%; padding-bottom: 2%;">
		<h2>Sign in:</h2>
		<input type="text" name="nam" placeholder="Username"/><br>
		<input type="password" name="pw" placeholder="Password" /><br>
		<input type="submit" /><br>
	No account? <a href="create_account.php">Create one here</a>
	</form>
<?php
} else echo "You're logged in!";
?>
<?php include_once("footer.php") ?>