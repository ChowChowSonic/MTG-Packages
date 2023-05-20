<?php include_once("header.php");
$voted = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
foreach ($packages as $id => $pack) { 
	if (key_exists($pack, $_POST)) {
		$voted[$id] = $db->prepare('SELECT 1 FROM votereciepts WHERE user_id = :id and card_name = :name and section = :sect;');
		$voted[$id]->bindValue("id", $_SESSION['id'], PDO::PARAM_INT); 
		$voted[$id]->bindValue("name", sanitize($_GET['name']), PDO::PARAM_STR); 
		$voted[$id]->bindValue("sect", sanitize($packageKeys[$pack]), PDO::PARAM_STR); 
		$voted[$id]->execute(); 
		$voted[$id] = $voted[$id]->fetch(); 
		if (!$voted[$id]) {
			$db->query('UPDATE cards SET ' . $pack . ' = ' . $pack . ' + ' . $_POST[$pack] . ' WHERE name = "' . sanitize($_GET['name']) . '";');
			$db->query('INSERT INTO votereciepts(user_id, card_name, section) VALUES (' . $_SESSION['id'] . ' ,"' . sanitize($_GET['name']) . '", "' . $packageKeys[$pack] . '");');
			$voted[$id]= 1;
		}
	}
}
$db->query('DELETE FROM votereciepts WHERE datediff(CURRENT_TIMESTAMP, votedate) > 7');
$card = $db->prepare('SELECT * FROM `cards` WHERE name = :name;');
$card->bindValue("name", sanitize($_GET['name']));
$card->execute();
$card = $card->fetch();
?>
<script>
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
<main style="height:800px;">
	<img src="./images/<?php echo getFileName($_GET['name']) ?>.jpg" alt="<?php echo getFileName(sanitize($_GET['name'])) ?>.jpg" style="float:left;">
	<?php if ($_SESSION['isLoggedIn'] == true) { ?>
		<form method="post">
			<table>
				<thead>
					<td class="green">"I think this card is a good example of..."</td>
					<td>Cumulative Score:</td>
					<td class="red">"I think this card is a bad example of..."</td>
				</thead>
				<?php foreach($packageKeys as $pack => $key){?>
				<tr>
					<td class="green"><input type="radio" class="green" name="<?php echo $pack;?>" value="1"  /> <h2 style="display:inline;"><?php echo $pack;?></h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card[$pack] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="<?php echo $pack;?>" value="-1"  /> <h2 style="display:inline;"><?php echo $pack;?></h2></td>
				</tr>
					<?php }?>
				<tr>
					<td>================================></td>
					<td><p style="text-align:center;"><input type="submit" value="Cast your votes!"></p></td>
					<td><================================</td>
				</tr>
			</table>
		</form>
	<?php } else {
		echo "Please log in to vote for cards!<br><h2>This card's ratings:</h2>";
		foreach ($packages as $pack) {
			?>
			<span class="reviewbox" style="width:100px"><?php echo $pack."<br>".$card[$pack]?></span>
		<?php
		}
	}
	?>
	<br>
	<!-- <a href=""><button>Purchase @ TCGPlayer (Affiliate Link)</button></a><br>
	<a href=""><button>Purchase @ CardKingdom (Affiliate Link)</button></a><br>
	<a href=""><button>Purchase @ CardHoarder (Affiliate Link)</button></a><br> -->

	<a href="https://scryfall.com/search?q=<?php echo sanitize($card['name']) ?>"><button>View on Scryfall</button></a><br>
</main>
<?php include_once("footer.php")?>