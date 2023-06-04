<?php include_once('header.php'); 
	$query = $db->prepare("SELECT 1 FROM `accounts` WHERE username = :x;");
	$query->bindValue("x", sanitize($_POST['nam']), PDO::PARAM_STR); 
	$query->execute(); 
	if($query->fetch() == 0){
    $hash = password_hash(sanitize($_POST['pw']), PASSWORD_DEFAULT);
    $query = "INSERT INTO accounts (username, password_hash, votecount)
              VALUES (:email, :password, 0)";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', sanitize($_POST['nam']));
    $statement->bindValue(':password', $hash);
    if($statement->execute()){
		echo "Congratulations! Your account has successfully been created!"; 
	}else{
		echo "Sorry, there was an issue on our end. Try again later!"; 
	}
    $statement->closeCursor();
	}else{
		echo "Sorry! This username already exists! Please try again using a different name!"; 
	}
?>
<script>
	window.location.href = "login.php";
</script>