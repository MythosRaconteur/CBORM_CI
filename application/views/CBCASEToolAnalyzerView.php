<html>
<head>
	<title>CB CASE Tool - Analysis</title>

<?php 
	$this->load->view("partials/header.php");
?>
</head>
<body bgcolor="#FFFFCC">
	<div class="mainContainer">
		<h2>CB CASE Tool</h2>
		<h4>Analysis</h4>
		<div>
			<form method="POST" action="generate">
				<input type="hidden" value="<?= $table ?>" name="tableName">
				<input type="hidden" value="<?= $author ?>" name="authorName">
				<input type="hidden" value="<?= $email ?>" name="authorEmail">
				<input type="hidden" value="<?= $company ?>" name="authorCompany">
				
				<p>The following items, based on table <span class="highlight"><?= strtoupper($table) ?></span>, will be created:</p>
				<div class="margin-left-40">
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="bo" class="form-control-label">Business Object (Model):</label>
							<input type="text" value=<?= $boName ?> name="bo" class="form-control" />
						</div>
						<div class="form-group col-lg-5">
							<label for="bofn" class="form-control-label">in file:</label>
							<input type="text" value=<?= $boFileName ?> name="bofn" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="bc" class="form-control-label">Business Object Collection:</label>
							<input type="text" value=<?= $bcName ?> name="bc" class="form-control" />
						</div>
						<div class="form-group col-lg-5">
							<label for="bcfn" class="form-control-label">in file:</label>
							<input type="text" value=<?= $bcFileName ?> name="bcfn" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="db" class="form-control-label">Data Broker:</label>
							<input type="text" value=<?= $brokerName ?> name="db" class="form-control" />
						</div>
						<div class="form-group col-lg-5">
							<label for="dbfn" class="form-control-label">in file:</label>
							<input type="text" value=<?= $brokerFileName ?> name="dbfn" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-10">
							<label for="path" class="form-control-label">Save output files to:</label>
							<input type="text" value="GeneratedClasses/" name="path" class="form-control" />
						</div>
					</div>
				</div>
				<p><hr width="65%" align="center" size="2" color="#AAAADD" /></p>
				<p>The following will be generated in the <span class="highlight"><?= $brokerName ?></span> class:</p>
				<div class="margin-left-40">
					<div class="row">
						<div class="form-group col-lg-10">
							<div class="col-lg-4">
								<label class="form-control-label">VARIABLE</label>
							</div>
							<div class="col-lg-3">
								<label class="form-control-label">COLUMN</label>
							</div>
							<div class="col-lg-1">
								<label class="form-control-label">GET</label>
							</div>
							<div class="col-lg-1">
								<label class="form-control-label">SET</label>
							</div>
						</div>
					</div>
<?php 
				foreach ($orMap as $colName => $varName) {
?>
					<div class="row">
						<div class="form-group col-lg-10">
							<div class="col-lg-4">
								<input type="text" value="<?= $varName ?>" name="<?= $colName ?>" class="form-control col-lg-3" />
							</div>
							<div class="col-lg-3">
								<label class="form-control-label vcenter"><?= $colName ?></label>
							</div>
							<div class="col-lg-1">
								<input type="checkbox" name="get<?= $colName ?>" checked />
							</div>
							<div class="col-lg-1">
								<input type="checkbox" name="set<?= $colName ?>" checked />
							</div>
						</div>
					</div>
<?php 
				}
?>
					<div class="row">
						<div class="form-group col-lg-5">
							<input type="submit" value="GENERATE" class="btn btn-primary" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
