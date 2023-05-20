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
	$db = new PDO("mysql:host=localhost;dbname=mtgpackages", "root", "");
} catch (PDOException $e) {
	//	echo $e->getMessage();
}


$packages = ["ramp", "draw", "tutor", "removal", "boardwipe", "wincon", "stax", "tokens", "blink", "recursion", "sacrifice"];
$packageKeys = [
	"ramp" => "RA", "draw" => "DR", "tutor" => "TU", "removal" => "RE", "boardwipe" => "BW", "wincon" => "WI", "stax" => "ST",
	"tokens" => "TO", "blink" => "BL", "recursion" => "GY", "sacrifice" => "SA"
];
$formats = ["Standard", "Pioneer", "Modern", "Legacy", "Vintage", "Pioneer", "Pauper", "Oathbreaker"];
$color_comb = "";
$maxResultPerPage = 60;
if (!key_exists('page', $_GET) || !is_numeric($_GET['page'])) $_GET['page'] = 0;
if (!key_exists('format', $_GET) || !in_array($_GET['format'], $formats)) $_GET['format'] = "1";

foreach (["W", "U", "B", "R", "G"] as $colors) {
	if (key_exists($colors, $_GET)) {
		$color_comb .= $colors;
	} else {
		$color_comb .= 'O';
	}
}
function sanitize(string $string)
{
	$str = strip_tags(filter_var(trim($string)));
	return $str;
}

function getFileName(string $string)
{
	$str = strip_tags(filter_var(trim($string)));
	$str = str_replace("'", "_", $str);
	$str = str_replace("\"", "_", $str);
	$str = str_replace("\\", "_", $str);
	$str = str_replace("/", "_", $str);
	$str = str_replace(":", "_", $str);
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
	<meta name="description" content="	MTG Packages is a community-based effort to find and organize the best spells of each archetype, 
										and rank them based on popularity! Select colors using the top pips, and click the category 
										to search for new cards! Not seeing your favorite card?	Vote for it to tell the world how great it is!">
	<!-- 
Have you finally come to the realization that 41 removal spells isn't enough for your mono-black deck? 
Are you tired of spending hours going through Scryfall searching for cards that would go good in your deck? 
Are you confused on what search term to use to find the ramp spells you need? 
Well say goodbye to spending hours on Scryfall looking for an archetype, and say hello to MTG Packages! MTG Packages is a community-based effort to 
find and organize the best spells in each archetype, and rank them based on popularity! Select a color combination using the 5 pips on the top, 
and click the category to search for new cards! Not seeing your favorite card? Vote for it to tell the world how great it is!
-->
	<meta name="keywords" content="MTG Packages, MTG, Packages, 
		Magic: The Gathering, Magic, Gathering<?php foreach ($packages as $pack) echo ", " . $pack . ", mtg best " . $pack . " spell, mtg " . $pack . " spells" ?>">
	<title>MTG Packages</title>
	<link rel="stylesheet" href="style.css">
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9181463624455686" crossorigin="anonymous"></script>
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
		<select name="format" id="format">
			<option value="1">All Formats</option>
			<?php foreach ($formats as $f) { ?>
				<option value="<?php echo $f; ?>"><?php echo $f; ?></option>
			<?php } ?>
		</select>
	</form>
	<form action="search.php" method="get">
		<input type="text" style="min-width:200px; width:25%;" onsubmit="htmlspecialchars();" name="query" placeholder="Search for a card by name..." />
		<input type="submit" value="search" />
	</form>

	<form style="height:2px;" action="<?php if ($_SESSION['isLoggedIn'] == true) echo "index.php";
										else echo "login.php"; ?>">
		<input style="position:absolute; right:5%; top:5%;" type="submit" name="<?php if ($_SESSION['isLoggedIn'] == true) echo "logout" ?>" value="<?php if ($_SESSION['isLoggedIn'] == true) echo "Logout";
																																					else echo "Login"; ?>">
	</form>
</header>
<?php if ($_SESSION["isLoggedIn"] == true) {
	echo "Welcome back, " . $_SESSION['user'] . "!<br>";
} ?>