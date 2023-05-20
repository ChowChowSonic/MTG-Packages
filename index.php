<?php 	include_once("header.php");
?>
<main><p style="text-align:center;">
<?php
// $json = json_decode(file_get_contents("min-cards.json")); 
if(!key_exists('package', $_GET) || !in_array(sanitize($_GET['package']), $packages))
	$_GET['package'] = 'ramp';
$sql = 'SELECT name, '.sanitize($_GET['package']).' FROM `cards` WHERE colors="'.$color_comb.
			'" AND '.sanitize($_GET['format']).' = 1 ORDER BY '.sanitize($_GET['package']).' DESC, name ASC LIMIT '.$maxResultPerPage.
			' OFFSET :off'; 
// echo $sql."<br>"; 
$json = $db->prepare($sql); 
$json->bindValue("off",  $maxResultPerPage*sanitize($_GET['page']) , PDO::PARAM_INT); 	
$json->execute();  $json = $json->fetchAll(); ?>
<div style="max-width:80%; text-align:center; margin-left:10%">
<?php
for($x=0; $x < sizeof($json); $x++){
	$card = $json[$x]; 
		?>
		<span class="reviewbox">
		<a href="./card.php?name=<?php echo sanitize($card['name'])."&".getWUBRG(); ?>"><img class="showcase" src="./images/<?php echo sanitize(getFileName($card['name'])); ?>.jpg"></img></a><br>
		<?php echo sanitize($_GET['package']); ?> Score: 
		<?php echo $card[sanitize($_GET['package'])]?>
		</span> 
	<?php } ?></div>
<form><p style="text-align:center;">
<button name="page" value="<?php echo max(0, sanitize($_GET['page'])-1);?>">Previous Page</button>
<button name="page" 
			value="<?php if(sizeof($json) == $maxResultPerPage) echo sanitize($_GET['page'])+1; else echo sanitize($_GET['page']); ?>">Next Page</button></p>
<?php 	if (key_exists('W', $_GET)) echo "<input type='hidden' name='W' value='on'/>"; 
		if (key_exists('U', $_GET)) echo "<input type='hidden' name='U' value='on'/>"; 
		if (key_exists('B', $_GET)) echo "<input type='hidden' name='B' value='on'/>"; 
		if (key_exists('R', $_GET)) echo "<input type='hidden' name='R' value='on'/>"; 
		if (key_exists('G', $_GET)) echo "<input type='hidden' name='G' value='on'/>"; 
		echo "<input type='hidden' name='package' value='".sanitize($_GET['package'])."'/>";;
		
		?>
</form>
</p>
</main>
<?php include_once("footer.php")?>