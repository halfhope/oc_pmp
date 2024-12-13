<div class="form-group" data-section="custom">
	<label class="col-sm-2 control-label" for="input-sql"><?php echo $entry_custom_sql ?></label>
	<div class="col-sm-10">
		<textarea name="sql" cols="30" rows="10" class="form-control" id="input-sql"><?php echo $sql ?></textarea>
	</div>
</div>
<?php if (isset($text_info)) { ?>
<div class="form-group" data-section="custom">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10">
		<div class="alert alert-info"><?php echo $text_info ?></div>
	</div>
</div>
<?php } ?>
<style>
#form-module .form-group[data-section="custom"] {
	background-color: #fff4d9;
	border-left: 5px solid #ffd166;
}
</style>