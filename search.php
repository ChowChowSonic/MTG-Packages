<?php include_once('header.php'); 
$sani = sanitize($_GET['query']); 
// $_GET['query'] = sanitize($_GET['query']);
$sql = "SELECT name FROM cards WHERE name LIKE :x;"; 
$query = $db->prepare($sql); 
$query->bindValue('x', '%'.$_GET['query'].'%', PDO::PARAM_STR); 
$query->execute(); 
$query = $query->fetchAll();
for($x=$maxResultPerPage*(sanitize($_GET['page'])); $x < sizeof($query) && $x < $maxResultPerPage*(sanitize($_GET['page'])+1); $x++){
	$card = $query[$x]; 
	if(sizeof($query) == 1){?>
		<script>
			window.location = "./card.php?name=<?php echo $card['name']."&".getWUBRG();?>";
		</script>
		<?php }
	?>
		<span class="reviewbox">
		<a href="./card.php?name=<?php echo sanitize($card['name'])."&".getWUBRG(); ?>"><img class="showcase" src="./images/<?php echo getFileName($card['name']); ?>.jpg"></img></a><br>
		</span> 
	<?php 
}
if(sizeof($query) > $maxResultPerPage){ ?>
	<form><p style="text-align:center;">
<button name="page" value="<?php echo max(0, sanitize($_GET['page'])-1);?>">Previous Page</button>
<button name="page" value="<?php echo min(intdiv(sizeof($query), $maxResultPerPage), sanitize($_GET['page'])+1);?>">Next Page</button></p>
<?php 	
		echo "<input type='hidden' name='query' value='".sanitize($_GET['query'])."'/></form>";;
}
if(sizeof($query) == 0){
//Insert witty comment on no cards being found here
?>
<p style="text-align:center;">
<img src="images/knuckles.jpg"/>
</p>
<h2 style="margin-left: 43%">No results were found!</h2>
<?php
}
?>