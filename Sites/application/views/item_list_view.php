<?php
	$this->render('common/meta');
	$this->render('common/header');
?>

<div>

<?php
	foreach($items as $item):
?>
	<tr>
		<td><?php echo $item->id; ?></td>
		<td><?php echo $item->name; ?></td>
	</tr>

<?php endforeach; ?>
</div>

<?php $this->render('common/footer'); ?>
