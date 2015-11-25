<html>
<head>
	<title>CB CASE Tool</title>
	
<?php 
	$this->load->view("partials/header.php");
?>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#dbSelector').change(function() {
		    	var dbSelected = $('select[name=database]').val();
		        var url = "http://localhost:8888/tables/" + dbSelected;
		        
		        $.get(url, function(res) {
			        if (res.length) {
				        $('#tableSelector').removeAttr("disabled");
			        }
			        
		        	$('#tableSelector').html(res);
		        }, 'html');
		        
		        // don't forget to return false so the page doesn't refresh
		        return false;
		    });
		});
	</script>
</head>
<body>
	<div class="mainContainer">
		<h2>CB CASE Tool</h2>
		<h4>Bootstrap</h4>
		<div>
			This application will assist you in the creation of object triplets:
			<ul>
				<li><strong>Business Object</strong> - A subclass of <em>CBBusinessObject</em>, and the place where business logic lives.</li>
				<li><strong>Business Object Collection</strong> - A subclass of <em>CBBusinessCollection</em>, which represents a group of business objects, and serves to model associative entities for the breaking up of many-to-many relationships, the capture of pertinent data therein, and the interface to the persisting of these relationships.</li>
				<li><strong>Data Broker</strong> - A subclass of <em>CBDataBroker</em>, which servers as the interface to the database, where object-to-relational mapping takes place.</li>
			</ul>
		</div>
		<hr width="65%" align="center" size="2" color="#AAAADD">
		<div>
			<form method="POST" action="analyze">
				<p>To begin, I need the following information:</p>
				<div class="margin-left-40">
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="database" class="form-control-label">Database to generate objects for:</label>
							<select name="database" class="form-control" id="dbSelector">
	<?php 
							foreach ($databases as $row) {
	?>
								<option value="<?= $row ?>"><?= $row ?></option>
	<?php
							}
	?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="table" class="form-control-label">Choose a Table:</label>
							<select name="table" class="form-control" id="tableSelector" disabled>
								<!-- Will be filled in via AJAX call from DB selection -->
								<option value="">You must select a DB to work with first</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="authorName" class="form-control-label">Author Name:</label><br />
							<input type="text" name="authorName" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="authorEmail" class="form-control-label">Author Email Address:</label><br />
							<input type="text" value="@codingdojo.com" name="authorEmail" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<label for="authorCompany" class="form-control-label">Author Company:</label><br />
							<input type="text" value="Coding Dojo" name="authorCompany" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-5">
							<input type="submit" value="PROCEED" class="btn btn-primary" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>