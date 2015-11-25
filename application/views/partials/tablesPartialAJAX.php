<?php 
	if (count($tables) > 0) {
		foreach($tables as $table) {
?>
			<option value="<?= $table ?>"><?= $table ?></option>
<?php
		}
	}
?>