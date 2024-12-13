<div class="form-group" data-section="global">
	<label class="col-sm-2 control-label" for="input-products"><?php echo $entry_products ?></label>
	<div class="col-sm-10">
		<input type="text" name="products_search" value="" placeholder="<?php echo $text_autocomplete ?>" list="list_products" id="input-products" class="form-control" />
		<datalist id="list_products"></datalist>
		<div id="product_id" class="well well-sm">
			<?php foreach ($products as $product) { ?>
			<div id="product_id<?php echo $product['id'] ?>"><i class="fa fa-minus-circle"></i> <?php echo $product['name'] ?>
			<input type="hidden" name="products[]" value="<?php echo $product['id'] ?>" />
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
$('#product_id').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
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
		$('input[name=\'products_search\']').val('');
		$('#product_id' + item['value']).remove();
		$('#product_id').append('<div id="product_id' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="products[]" value="' + item['value'] + '" /></div>');
		$('#product_id #product_id' + item['value']).click()
	}
});
</script>

<style>
#form-module .form-group[data-section="global"] {
	background-color: #fff4d9;
	border-left: 5px solid #ffd166;
}
</style>