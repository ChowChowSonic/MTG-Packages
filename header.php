<?php
session_start();
if (!key_exists("isLoggedIn", $_SESSION)) {
	$_SESSION["isLoggedIn"] = false;
	$_SESSION["user"] = false;
	$_SESSION["id"] = false;
}
if (key_exists("logout", $_GET)) {
	$_SESSION["isLoggedIn"] = false;
	$_SESSION["user"] = false;
	$_SESSION["id"] = false;
	session_destroy();
}
try {
	$db = new PDO("mysql:host=localhost;dbname=mtgpackages", "account", "Sonic$1367");
} catch (PDOException $e) {
	//	echo $e->getMessage();
}

if(!key_exists('page', $_GET)) $_GET['page'] = 0; 
$packages = ["ramp", "draw", "tutor", "removal", "boardwipe", "wincon", "stax"];
$color_comb = "";
$maxResultPerPage = 60; 

foreach (["W", "U", "B", "R", "G"] as $colors) {
	if (key_exists($colors, $_GET)) {
		$color_comb .= $colors;
	} else {
		$color_comb .= 'O';
	}
}
function sanitize(string $string){
	$str = strip_tags(html_entity_decode(htmlspecialchars(filter_var(trim($string)))));
	return $str; 
}

function getFileName(string $string){
	$str = strip_tags(html_entity_decode(htmlspecialchars(filter_var(trim($string)))));
	$str = str_replace("'", "_", $str);
	$str = str_replace("\"", "_", $str);
	$str = str_replace("\\", "_", $str);
	$str = str_replace("/", "_", $str);
	return $str; 
}

function compareColors(string $colors, array $filecolors)
{
	$split = str_split($colors);
	if ($split == $filecolors) return true;
	foreach ($split as $character) {
		if (!in_array($character, $filecolors)) return false;
	}
	foreach ($filecolors as $character) {
		if (!in_array($character, $split)) return false;
	}
	return true;
}
function getWUBRG()
{
	$str = "";
	if (key_exists('W', $_GET)) $str .= "W=on";
	if (key_exists('U', $_GET)) $str .= "&U=on";
	if (key_exists('B', $_GET)) $str .= "&B=on";
	if (key_exists('R', $_GET)) $str .= "&R=on";
	if (key_exists('G', $_GET)) $str .= "&G=on";
	return $str;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="ASCII">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MTG Packages</title>
	<link rel="stylesheet" href="style.css">
</head>
<header class="top">
	<h1>MTG Packages</h1>
	<form action="index.php" id="colors" method="get">
		<input type="checkbox" name="W" id="W" class="colorpicker" <?php if (key_exists('W', $_GET)) echo 'checked=""' ?>>
		<input type="checkbox" name="U" id="U" class="colorpicker" <?php if (key_exists('U', $_GET)) echo 'checked=""' ?>>
		<input type="checkbox" name="B" id="B" class="colorpicker" <?php if (key_exists('B', $_GET)) echo 'checked=""' ?>>
		<input type="checkbox" name="R" id="R" class="colorpicker" <?php if (key_exists('R', $_GET)) echo 'checked=""' ?>>
		<input type="checkbox" name="G" id="G" class="colorpicker" <?php if (key_exists('G', $_GET)) echo 'checked=""' ?>>
		<br>
		<?php foreach ($packages as $pack) { ?>
			<input type="submit" onsubmit="htmlspecialchars();" name="package" value="<?php echo $pack; ?>" />
		<?php } ?>
	</form>
	<form action="search.php" method="get">
		<input type="text" style="width:25%;" onsubmit="htmlspecialchars();" name="query" placeholder="Search for a card by name..." />
		<input type="submit" value="search" />
	</form>

	<form action="<?php if ($_SESSION['isLoggedIn'] == true) echo "index.php";
					else echo "login.php"; ?>">
		<input style="position:relative; right:-45%; top:-160px;" type="submit" name="<?php if ($_SESSION['isLoggedIn'] == true) echo "logout" ?>" 
		value="<?php if ($_SESSION['isLoggedIn'] == true) echo "Logout"; else echo "login/Create Account"; ?>">
	</form>
</header>
<?php if ($_SESSION["isLoggedIn"] == true) {
	echo "Welcome back, " . $_SESSION['user'] . "!<br>";
} ?>