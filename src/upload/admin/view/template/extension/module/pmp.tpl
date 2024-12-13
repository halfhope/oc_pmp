<?php echo $header ?><?php echo $column_left ?>
<div id="content">
<style>
html {
	overflow-y:scroll;
}
#spinner.active {
  border: 4px solid #80b1cb; /* Light grey */
  border-top: 4px solid #1E91CF; /* Blue */
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: block;
  position: absolute;
  animation: spin 1s linear infinite;
}
#data_source_form {
	position: relative;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
@keyframes color {
  0% { background: rgba(6,6,6,0) }
  100% { background: rgba(6,6,6,.2); }
}
#content .panel-body{
	padding-top:0px;
	padding-bottom:0px;
}
button.btn {
	outline: none !important;
}
#form-module .control-label {
	padding-top:0;
}
#form-module .form-group {
	padding-top: 10px;
	padding-bottom: 10px;
	display: flex;
    align-items: center;
	border-left: 5px solid #dfdfdf;
}
.form-group + #data_source_form[class="form-group"] {
    border-top: 1px dashed #c1c1c1;
}
#data_source_form[class="form-group"] + .form-group {
    border-top: 1px dashed #c1c1c1;
}
.form-group#data_source_form{
    margin: 0px -15px 0px -15px;
	padding:0px;
    display: block;
}
#data_source_form .form-group{
	margin-left:-5px;
	margin-right:0;
}
#form-module .well-sm {
	padding: 7px;
	margin-bottom:10px;
	max-height:200px;
	overflow-y:scroll;
	min-height:36.56px!important;
}
#dynamic_description .well-sm {
	width: 100%;
    display: block;
}
#form-module .alert {
	margin-bottom: 0;
}
#form-module .well-sm .checkbox {
	min-height:unset;
	padding-top: 0px;
	padding-bottom: 0;
	border: 0;
}
#form-module .well-sm .checkbox label {
	display: flex;
	justify-content: flex-start;
	align-items: center;
	padding-left: 15px;
}
#form-module .well-sm .checkbox label input[type="checkbox"] {
	margin-right: 5px;
}
#form-module .well-sm .table {
	margin: 0;
}
#form-module .well-sm .table tbody tr {
	background:none;
}
#form-module .switch input[type="radio"] {
	display:none;
}
#form-module .form-group[data-section="pmp"] {
	background-color: #f4ffed;
	border-left: 5px solid #b1db95;
}
#form-module input[type="text"], #form-module select {
	height: 36.56px!important;
}
#form-module input[name="products_search"] {
	margin-bottom: 5px!important;
}
option[value="rd_recommendation"],option[value="rd_similar"]{
	color:#3f3;
}
</style>
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel ?>" data-toggle="tooltip" title="<?php echo $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>				
				<li><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
			<div class="alert alert-danger alert-dismissible" data-countdown><i class="fa fa-exclamation-circle"></i>&nbsp;<span class="countdown">[2]</span> <?php echo $error_warning ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success alert-dismissible" data-countdown><i class="fa fa-check-circle"></i>&nbsp;<span class="countdown">[2]</span> <?php echo $success ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit ?></h3>
				<?php if (isset($sections)) { ?>
				<div class="pull-right">
					<?php foreach ($sections as $section) { ?>
					<a href="<?php echo $section['href'] ?>"><i class="fa <?php echo $section['icon'] ?>"></i>&nbsp;<?php echo $section['text'] ?></a>&nbsp;
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
					
					<div class="form-group" data-section="default">
						<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name ?></label>
						<div class="col-sm-10">
							
							<div class="input-group">
								<span class="input-group-btn"><button class="btn btn-default" id="button-fill"><?php echo $button_fill ?></button></span>
								<input type="text" name="name" value="<?php echo $name ?>" placeholder="<?php echo $entry_name ?>" id="input-name" class="form-control" />
							</div>

						</div>
					</div>

					<div class="form-group" data-section="default">
						<label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_limit ?></label>
						<div class="col-sm-10">
							<input type="text" name="limit" value="<?php echo $limit ?>" placeholder="<?php echo $entry_limit ?>" id="input-limit" class="form-control" />
						</div>
					</div>

					<div class="form-group" data-section="default">
						<label class="col-sm-2 control-label" for="input-image_width"><?php echo $entry_image_size ?></label>
						<div class="col-sm-10">
							<div class="row">
								<div class="col-sm-6">
									<input type="text" name="width" value="<?php echo $width ?>" placeholder="<?php echo $entry_image_width ?>" id="input-image_width" class="form-control" />	
								</div>
								<div class="col-sm-6">
									<input type="text" name="height" value="<?php echo $height ?>" placeholder="<?php echo $entry_image_height ?>" id="input-image_height" class="form-control" />	
								</div>
							</div>
						</div>
					</div>

					<div class="form-group" data-section="default">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status ?></label>
						<div class="col-sm-10">
							<select name="status" id="input-status" class="form-control">
								<?php if ($status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled ?></option>
								<option value="0"><?php echo $text_disabled ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title ?></label>
						<div class="col-sm-10">
							<div class="row">
							<?php foreach ($languages as $language_key => $language) { ?>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon"><img src="language/<?php echo $language['code'] ?>/<?php echo $language['code'] ?>.png"></span>
										<input type="text" name="title[<?php echo $language['language_id'] ?>]" value="<?php echo isset($title[$language['language_id']]) ? $title[$language['language_id']] : '' ?>" placeholder="<?php echo $language['name'] ?>" class="form-control">
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-data_source"><i id="spinner"></i> <?php echo $entry_data_source ?> </label>
						<div class="col-sm-10">
							<select name="data_source" id="input-data_source" class="form-control">
								<?php foreach ($groups as $group_data) { ?>
									<optgroup label="<?php echo $group_data['text'] ?>">
									<?php foreach ($group_data['datasources'] as $datasource) { ?>
										<?php if ($data_source == $datasource['code'] ) { ?>
										<option value="<?php echo $datasource['code'] ?>" selected="selected"><?php echo $datasource['text'] ?></option>
										<?php } else { ?>
										<option value="<?php echo $datasource['code'] ?>"><?php echo $datasource['text'] ?></option>
										<?php } ?>
									<?php } ?>
								</optgroup>
								<?php } ?>
							</select>
						</div>
					</div>
					<?php if (isset($data_source_form_data)) { ?>
					<div id="data_source_form" class="form-group">
					<?php echo $data_source_form_data ?>
					</div>
					<?php } else { ?>
					<div id="data_source_form" class="form-group hidden">
					<?php echo $data_source_form_data ?>
					</div>
					<?php } ?>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-shuffle"><span data-toggle="tooltip" title="<?php echo $entry_shuffle_help ?>"><?php echo $entry_shuffle ?></span></label>
						<div class="col-sm-10">
							<div class="switch btn-group">
								<label class="btn btn-default <?php echo ($shuffle) ? 'active' : '' ?>"> 
									<input type="radio" name="shuffle" value="1" <?php echo ($shuffle) ? 'checked="checked"' : '' ?>/><?php echo $text_yes ?>
								</label>
								<label class="btn btn-default <?php echo (!$shuffle) ? 'active' : '' ?>"> 
									<input type="radio" name="shuffle" value="0" <?php echo (!$shuffle) ? 'checked="checked"' : '' ?>/><?php echo $text_no ?>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-cache"><?php echo $entry_cache ?></label>
						<div class="col-sm-10">
							<div class="input-group">
								<span class="input-group-btn switch">
										<label class="btn btn-default <?php echo ($cache) ? 'active' : '' ?>"> 
											<input type="radio" name="cache" value="1" <?php echo ($cache) ? 'checked="checked"' : '' ?>/><?php echo $text_yes ?>
										</label>
										<label class="btn btn-default <?php echo (!$cache) ? 'active' : '' ?>"> 
											<input type="radio" name="cache" value="0" <?php echo (!$cache) ? 'checked="checked"' : '' ?>/><?php echo $text_no ?>
										</label>
								</span>
								<select name="cache_expire" id="input-cache" class="form-control">
									<?php foreach ($cache_expire_options as $text => $value) { ?>
										<?php if ($cache_expire == $value) { ?>
										<option value="<?php echo $value ?>" selected="selected"><?php echo $text ?></option>
										<?php } else { ?>
										<option value="<?php echo $value ?>"><?php echo $text ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-compat"><span data-toggle="tooltip" title="<?php echo $entry_compat_help ?>"><?php echo $entry_compat ?></span></label>
						<div class="col-sm-10">
							<div class="switch btn-group">
								<label class="btn btn-default <?php echo ($compat) ? 'active' : '' ?>"> 
									<input type="radio" name="compat" value="1" <?php echo ($compat) ? 'checked="checked"' : '' ?>/><?php echo $text_yes ?>
								</label>
								<label class="btn btn-default <?php echo (!$compat) ? 'active' : '' ?>"> 
									<input type="radio" name="compat" value="0" <?php echo (!$compat) ? 'checked="checked"' : '' ?>/><?php echo $text_no ?>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for="input-template"><span data-toggle="tooltip" title="<?php echo $entry_template_help ?>"><?php echo $entry_template ?></span></label>
						<div class="col-sm-10">
							<input type="text" name="template" value="<?php echo $template ?>" placeholder="<?php echo $entry_template ?>" id="input-template" class="form-control" />
						</div>
					</div>

					<div class="form-group" data-section="pmp">
						<label class="col-sm-2 control-label" for=""><?php echo $entry_dynamic_desc ?></label>
						<div class="col-sm-10" id="dynamic_description">
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {

	function initGroup() {
		$('select[name=data_source]').trigger('change');
	}

	function generateDynamicDescription() {
		let controller, view;
		if ($('input[name=compat]:checked').val() == 1) {
			controller = 'extension/module/featured';
		} else {
			controller = 'extension/module/pmp';
		}
		if ($('input[name=template]').val() !== '') {
			view = $('input[name=template]').val();
		} else {
			view = controller;
		}
		$('#dynamic_description').html(`<div class="alert alert-info"><?php echo $entry_dynamic_desc_data ?></div>`);
	}
	
	$('input[type=radio][name=compat]').on('change', generateDynamicDescription);
	$('input[name=template]').on('input', generateDynamicDescription);

	$('select[name=data_source]').on('change', function(e) {
		let datasource = $('select[name=data_source] option:selected').val();
		$.ajax({
			url: '<?php echo $data_source_link ?>',
			dataType: 'html',
			method: 'get',
			data : {
				datasource: encodeURIComponent(datasource),
				module_id: <?php echo $module_id ?>
			},
			beforeSend: function() {
				$('select[name=data_source]').prop('disabled', true);
				$('#spinner').addClass('active');
				$('#form-module [data-toggle=tooltip]').tooltip('destroy');
			},
			success: function(html) {
				$('#data_source_form').html(html);
				if ($('#data_source_form').html().trim() == '') {
					$('#data_source_form').addClass('hidden');
				} else {
					$('#data_source_form').removeClass('hidden');
				}
			},
			complete: function() {
				$('#spinner').removeClass('active');				
				$('select[name=data_source]').prop('disabled', false);
				$('#form_module [data-toggle=tooltip]').tooltip({container:'body'});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#button-fill').on('click', function(e) {
		e.preventDefault();
		let item_selected = $('select[name=data_source] option:selected');
		let group_text = item_selected.text().trim();
		let mode_text = $('#data_source_form').find('input[name=mode]:checked').closest('label').text().trim();
		if (mode_text !== '') {
			group_text += ' - ' + mode_text;
		}
		$('#input-name').val(group_text);
	});
	
	$('#form-module').delegate('.switch > label', 'click', function(e) {
		$(this).closest('.switch').find('label').removeClass('active');
		$(this).toggleClass('active');
		$(this).find('input').prop('checked', true).trigger('change');
	});

	$('[data-countdown]').each(function(index, value) {
		let tid = setInterval(() => {
			let current = parseInt($(value).find('.countdown').text().match(/(\d+)/));
			$(value).find('.countdown').text('[' + (current - 1) + ']');
			if (current == 1) {
				clearInterval(tid);
				$(value).remove();
			}
		}, 1000);
	});

	generateDynamicDescription();

});
</script>
<?php echo $footer ?>