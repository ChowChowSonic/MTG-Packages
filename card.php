<?php include_once("header.php");
$voted = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
foreach ($packages as $id => $pack) {
	if (key_exists($pack, $_POST)) {
		$key = strtoupper($pack[0] . $pack[1]);
		$voted[$id] = $db->prepare('SELECT 1 FROM votereciepts WHERE user_id = :id and card_name = :name and section = "' . $key . '";');
		$voted[$id]->bindValue("id", $_SESSION['id'], PDO::PARAM_INT); 
		$voted[$id]->bindValue("name", sanitize($_GET['name']), PDO::PARAM_STR); 
		$voted[$id]->execute(); 
		$voted[$id] = $voted[$id]->fetch(); 
		if ($voted[$id] == 0) {
			$db->query('UPDATE cards SET ' . $pack . ' = ' . $pack . ' + ' . $_POST[$pack] . ' WHERE name = "' . sanitize($_GET['name']) . '";');
			$db->query('INSERT INTO votereciepts(user_id, card_name, section) VALUES (' . $_SESSION['id'] . ' ,"' . sanitize($_GET['name']) . '", "' . $key . '");');
			$voted[$id]++;
		}
	}
}
$card = $db->prepare('SELECT * FROM `cards` WHERE name = "' . sanitize($_GET['name']) . '";');
$card->execute();
$card = $card->fetch();
?>
<script>
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
<main>
	<img src="./images/<?php echo getFileName($_GET['name']) ?>.jpg" alt="<?php echo getFileName(sanitize($_GET['name'])) ?>.jpg" style="float:left;">
	<?php if ($_SESSION['isLoggedIn'] == true) { ?>
		<form method="post">
			<table>
				<thead>
					<td class="green">"I think this card is a good example of..."</td>
					<td>Cumulative Score:</td>
					<td class="red">"I think this card is a bad example of..."</td>
				</thead>
				<tr>
					<td class="green"><input type="radio" class="green" name="ramp" value="1"  /> <h2 style="display:inline;">Ramp</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['ramp'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="ramp" value="-1"  /> <h2 style="display:inline;">Ramp</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green"name="draw" value="1"  /> <h2 style="display:inline;">Draw</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['draw'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="draw" value="-1"  /> <h2 style="display:inline;">Draw</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green" name="tutor" value="1"  /> <h2 style="display:inline;">Tutor</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['tutor'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="tutor" value="-1"  /> <h2 style="display:inline;">Tutor</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green" name="removal" value="1"  /> <h2 style="display:inline;">removal</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['removal'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="removal" value="-1"  /> <h2 style="display:inline;">removal</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green" name="boardwipe" value="1"  /> <h2 style="display:inline;">Boardwipe</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['boardwipe'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="boardwipe" value="-1"  /> <h2 style="display:inline;">Boardwipe</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green" name="wincon" value="1"  /> <h2 style="display:inline;">Wincon</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['wincon'] ?></h2></td>
					<td class="red"><input type="radio" class="red" name="wincon" value="-1"  /> <h2 style="display:inline;">Wincon</h2></td>
				<tr>
				<tr>
					<td class="green"><input type="radio" class="green" name="stax" value="1"  /> <h2 style="display:inline;">Stax</h2></td>
					<td class="center"><h2 style="display:inline;"><?php echo $card['stax'] ?></h2></td>
					<td class="red"><input type="radio" name="stax" value="-1"  /> <h2 style="display:inline;">Stax</h2></td>
				<tr>
				<tr>
					<td>================================></td>
					<td><p style="text-align:center;"><input type="submit" value="Cast your votes!"></p></td>
					<td><==============================</td>
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
</main>