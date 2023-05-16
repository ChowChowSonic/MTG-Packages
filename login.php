<?php include_once('header.php'); 

if(key_exists("pw", $_POST) && key_exists("nam", $_POST)){
	$sql = "SELECT user_id, username, password_hash from `accounts` WHERE username = \"".sanitize($_POST['nam'])."\";"; 
	$usr = $db->prepare($sql);//"SELECT (`emailAddress`, `password`, `fName`, `lName`) from `jewelryManagers` WHERE `emailAddress` == \"$_POST['pw']\""
	$usr->execute();
	$usr = $usr->fetch();  

	if($usr != false && password_verify(sanitize($_POST["pw"]), $usr["password_hash"])){
		$_SESSION["isLoggedIn"]= true; 
		$_SESSION["user"]=$usr['username'];
		$_SESSION["id"]=$usr['user_id'];
	}else echo "Password was incorrect, please try again."; 
}

	if($_SESSION["isLoggedIn"] == false){ ?>
	<form action="login.php" method="post">
		<input type="text" name="nam"/><br>
		<input type="password" name="pw"/><br>
		<input type="submit"/><br>
	</form>
		<p>No account? <a href="create_account.php">Create one here</a></p>
<?php 
}else echo "You're logged in!"; 
	?>