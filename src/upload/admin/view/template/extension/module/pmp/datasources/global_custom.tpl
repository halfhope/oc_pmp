
<script src="view/javascript/jquery/Sortable.js"></script>
<script src="view/javascript/jquery/jquery-sortable.js"></script>

<div class="form-group" data-section="global">
	<label class="col-sm-2 control-label" for="input-products"><?php echo $entry_products ?></label>
	<div class="col-sm-10">
		<input type="text" name="products_search" value="" placeholder="<?php echo $text_autocomplete ?>" list="list_products" id="input-products" class="form-control" />
		<datalist id="list_products"></datalist>
		<div id="product_id" class="well well-sm">
			<?php foreach ($products as $product) { ?>
			<div id="product_id<?php echo $product['id'] ?>" class="well-sm-item"><i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<i class="fa fa-hand-grab-o"></i>&nbsp;&nbsp;<?php echo $product['name'] ?>
			<input type="hidden" name="products[<?php echo $product['id'] ?>][sort_order]" value="<?php echo $product['sort_order'] ?>" />
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div class="form-group" data-section="global">
	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_stock_status_help ?>"><?php echo $entry_stock_status ?></span></label>
	<div class="col-sm-10">
		<div class="well well-sm">
			<table class="table table-striped">
			<?php foreach ($stock_statuses as $stock_status_key => $stock_status_data) { ?>
			<tr class="form-label">
				<td class="checkbox">
				<label>
				<?php if (in_array($stock_status_data['stock_status_id'], $stock_status)) { ?>
					<input type="checkbox" name="stock_status[]" class="form-control" value="<?php echo $stock_status_data['stock_status_id'] ?>" checked="checked" />
					<?php echo $stock_status_data['name'] ?>
					<?php } else { ?>
					<input type="checkbox" name="stock_status[]" class="form-control" value="<?php echo $stock_status_data['stock_status_id'] ?>" />
					<?php echo $stock_status_data['name'] ?>
					<?php } ?>
				</label> 
				</td>
			</tr>
			<?php } ?>
			</table>
		</div>
		<a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', true);return false;"><?php echo $text_select_all ?></a> / <a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', false);return false;"><?php echo $text_unselect_all ?></a> / <a href="#" onclick="invertSelection(this);return false;"><?php echo $text_invert_selection ?></a>
	</div>
</div>

<div class="form-group" data-section="global">
	<label class="col-sm-2 control-label" for="input-quantity_expression"><?php echo $entry_quantity ?></label>
	<div class="col-sm-10">
		<div class="row">
			<div class="col-sm-6">
				<select name="quantity_expression" id="input-quantity_expression" class="form-control">
				<?php foreach ($qty_expressions as $expression) { ?>
				<?php if ($quantity_expression == $expression) { ?>
					<option value="<?php echo $expression ?>" selected="selected"><?php echo $expression ?></option>
					<?php } else { ?>
						<option value="<?php echo $expression ?>"><?php echo $expression ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</div>
			<div class="col-sm-6">
				<input type="text" name="quantity" value="<?php echo $quantity ?>" placeholder="<?php echo $entry_quantity ?>" id="input-quantity" class="form-control" />
			</div>
		</div>
	</div>
</div>

<div class="form-group" data-section="global">
	<label class="col-sm-2 control-label" for="input-sort"><?php echo $entry_sort_order ?></label>
	<div class="col-sm-10">
		<div class="row">
			<div class="col-sm-6">
				<select name="sort" id="input-sort" class="form-control">
					<?php foreach ($sorts as $sort_index => $sort_name) { ?>
						<?php if ($sort == $sort_index) { ?>
						<option data-group="<?php echo (isset($group_index) ? $group_index : '') ?>" value="<?php echo $sort_index ?>" selected="selected"><?php echo $sort_name ?></option>
						<?php } else { ?>
						<option data-group="<?php echo (isset($group_index) ? $group_index : '') ?>" value="<?php echo $sort_index ?>"><?php echo $sort_name ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-6">
				<select name="order" id="input-order" class="form-control">
					<?php foreach ($orders as $order_index => $order_name) { ?>
						<?php if ($order == $order_index) { ?>
						<option data-group="<?php echo (isset($group_index) ? $group_index : '') ?>" value="<?php echo $order_index ?>" selected="selected"><?php echo $order_name ?></option>
						<?php } else { ?>
						<option data-group="<?php echo (isset($group_index) ? $group_index : '') ?>" value="<?php echo $order_index ?>"><?php echo $order_name ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>
</div>
<script>
$('#product_id').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

$('#product_id').sortable({
	handle: '.well-sm-item',
	animation: 150,
	onEnd: function (evt) {
		var orderIndex = 1;
		$($(evt.item).parent().find('input[name*="sort_order"]')).each(function() {
			$(this).val(orderIndex);
			orderIndex++;
		});
	}
});

$('input[name=\'products_search\']').autocomplete({
	source: function(request, response) {
		$.ajax({
			url: '<?php echo $autocomplete ?>' + '&term=' + encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json.results, function(item) {
					return {
						label: item['text'],
						value: item['id']
					}
				}));
			}
		});
	},
	select: function(item) {
		let sort_order = $('#product_id .well-sm-item').length;
		$('input[name=\'products_search\']').val('');
		$('#product_id' + item['value']).remove();
		$('#product_id').append('<div id="product_id' + item['value'] + '" class="well-sm-item"><i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<i class="fa fa-hand-grab-o"></i>&nbsp;&nbsp;' + item['label'] + '<input type="hidden" name="products[' + item['value'] + '][sort_order]" value="' + sort_order + '" /></div>');
		$('#product_id #product_id' + item['value']).click()
	}
});
</script>

<style>
#form-module .form-group[data-section="global"] {
	background-color: #fff4d9;
	border-left: 5px solid #ffd166;
}
.well-sm-item {
	line-height: 2em;
	padding: 2px;
}
.well-sm-item + .well-sm-item {
	border-top: 1px solid #fff;
}
.well-sm-item input.well-sm-input {
	width: 50px;
	height: 26px!important;
}
.well-sm-item i.fa.fa-minus-circle:hover {
	cursor: pointer;
}
.well-sm-item i.fa.fa-hand-grab-o:hover {
	cursor: grab;
}
</style>