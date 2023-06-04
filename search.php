<?php include_once('header.php');
$sani = sanitize($_GET['query']);
// $_GET['query'] = sanitize($_GET['query']);
$sql = "SELECT name FROM cards WHERE name LIKE :x LIMIT :max OFFSET :offs";
$query = $db->prepare($sql);
$query->bindValue('x', '%' . $_GET['query'] . '%', PDO::PARAM_STR);
$query->bindValue('max', $maxResultPerPage, PDO::PARAM_INT);
$query->bindValue('offs',  $maxResultPerPage * sanitize($_GET['page']), PDO::PARAM_INT);
$query->execute();
$query = $query->fetchAll(); ?>
<p style="text-align:center;">
</p>
<?php
for ($x = 0; $x < sizeof($query); $x++) {
	$card = $query[$x];
	if (sizeof($query) == 1) { ?>
		<script>
			window.location = "./card.php?name=<?php echo $card['name'] . "&" . getWUBRG(); ?>";
		</script>
	<?php }
	?>
	<span class="reviewbox">
		<a href="./card.php?name=<?php echo sanitize($card['name']) . "&" . getWUBRG(); ?>"><img class="showcase" src="./images/<?php echo getFileName($card['name']); ?>.jpg"></img></a><br>
	</span>
<?php
} ?>

<?php
if (sizeof($query) == 0) {
	//Insert witty comment on no cards being found here
?>
	<p style="text-align:center;">
		<img src="images/knuckles.jpg" />
	</p>
	<h2 style="margin-left: 43%">No results were found!</h2>
<?php
} else {
?>
	<form>
		<p style="text-align:center;">
			<button name="page" value="<?php echo max(0, sanitize($_GET['page']) - 1); ?>">Previous Page</button>
			<?php
			for ($x = 3; $x > 0; $x--)
				if ($_GET['page'] - $x >= 0) { ?>
				<button name="page" value="<?php echo max(0, sanitize($_GET['page']) - $x); ?>"><?php echo max(0, sanitize($_GET['page']) - $x); ?></button>
			<?php } ?>
			<button name="page" disabled="true" value="<?php echo sanitize($_GET['page']) ?>"><?php echo sanitize($_GET['page']) ?></button>
			<?php
			for ($x = 1; $x <= 3; $x++)
				if ($_GET['page'] + $x >= 0 && count($query) == $maxResultPerPage) { ?>
				<button name="page" value="<?php echo max(0, sanitize($_GET['page']) + $x); ?>"><?php echo max(0, sanitize($_GET['page']) + $x); ?></button>
			<?php } ?>
			<button name="page" value="<?php if (sizeof($query) == $maxResultPerPage) echo sanitize($_GET['page']) + 1;
										else echo sanitize($_GET['page']); ?>">Next Page</button>
		</p>
		<input type='hidden' name='query' value='<?php echo sanitize($_GET['query']); ?>'>
	</form>
<?php }
include_once("footer.php"); ?>