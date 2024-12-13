
<div class="form-group" data-section="arg">
    <label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_arg_mode ?></label>
    <div class="col-sm-10">
        <div class="mode switch btn-group">
            <label data-toggle="tooltip" title="<?php echo $help_arg_mode_abs ?>" class="btn btn-default <?php echo ($mode == 1) ? 'active' : '' ?>" <?php echo ($mode == 1) ? 'active' : '' ?>>
                <input type="radio" name="mode" value="1" data-group="mode_abs" <?php echo ($mode == 1) ? 'checked="checked"' : '' ?>><?php echo $text_arg_mode_abs ?>
            </label>
            <label data-toggle="tooltip" title="<?php echo $help_arg_mode_rel ?>" class="btn btn-default <?php echo ($mode == 2) ? 'active' : '' ?>" <?php echo ($mode == 2) ? 'active' : '' ?>>
                <input type="radio" name="mode" value="2" data-group="mode_rel" <?php echo ($mode == 2) ? 'checked="checked"' : '' ?>><?php echo $text_arg_mode_rel ?>
            </label>
        </div>
        <div class="pull-right checkbox">
            <label data-toggle="tooltip" title="<?php echo $entry_arg_invert_help ?>" class="control-label">
                <input type="checkbox" name="invert" value="1" <?php echo ($invert) ? 'checked' : '' ?>>&nbsp;<?php echo $entry_arg_invert ?>
            </label>
        </div>
    </div>
</div>
<?php if (isset($categories)) { ?>
<div class="form-group" data-mode="mode_abs" data-section="arg">
    <label class="col-sm-2 control-label"><?php echo $entry_categories ?></label>
    <div class="col-sm-10">
        <div class="well well-sm">
            <table class="table table-striped">
            <tr>
            <?php foreach ($categories as $category_key => $category) { ?>
            <td class="checkbox">
            <label>
                <?php if (in_array($category['category_id'], $selected_categories)) { ?>
                <input type="checkbox" name="selected_categories[]" value="<?php echo $category['category_id'] ?>" checked="checked" />
                <?php echo $category['name'] ?>
                <?php } else { ?>
                <input type="checkbox" name="selected_categories[]" value="<?php echo $category['category_id'] ?>" />
                <?php echo $category['name'] ?>
                <?php } ?>
            </label> 
            </td>
            <?php } ?>
            </tr>
            </table>
        </div>
        <a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', true);return false;"><?php echo $text_select_all ?></a> / <a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', false);return false;"><?php echo $text_unselect_all ?></a> / <a href="#" onclick="invertSelection(this);return false;"><?php echo $text_invert_selection ?></a>
    </div>
</div>
<?php } ?>
<?php if (isset($manufacturers)) { ?>
<div class="form-group" data-mode="mode_abs" data-section="arg">
    <label class="col-sm-2 control-label"><?php echo $entry_manufacturers ?></label>
    <div class="col-sm-10">
        <div class="well well-sm">
            <table class="table table-striped">
            <tr>
            <?php foreach ($manufacturers as $manufacturer_key => $manufacturer) { ?>
            <td class="checkbox">
            <label>
                <?php if (in_array($manufacturer['manufacturer_id'], $selected_manufacturers)) { ?>
                <input type="checkbox" name="selected_manufacturers[]" value="<?php echo $manufacturer['manufacturer_id'] ?>" checked="checked" />
                <?php echo $manufacturer['name'] ?>
                <?php } else { ?>
                <input type="checkbox" name="selected_manufacturers[]" value="<?php echo $manufacturer['manufacturer_id'] ?>" />
                <?php echo $manufacturer['name'] ?>
                <?php } ?>
            </label> 
            </td>
            <?php } ?>
            </tr>
            </table>
        </div>
        <a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', true);return false;"><?php echo $text_select_all ?></a> / <a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', false);return false;"><?php echo $text_unselect_all ?></a> / <a href="#" onclick="invertSelection(this);return false;"><?php echo $text_invert_selection ?></a>
    </div>
</div>
<?php } ?>

<?php if (isset($consider_categories)) { ?>
<div class="form-group" data-mode="mode_rel" data-section="arg">
    <label class="col-sm-2 control-label" for="input-consider_categories"><span data-toggle="tooltip" title="<?php echo $entry_consider_categories_help ?>"><?php echo $entry_consider_categories ?></span></label>
    <div class="col-sm-10">
        <div class="switch btn-group">
            <label class="btn btn-default <?php echo ($consider_categories) ? 'active' : '' ?>"> 
                <input type="radio" name="consider_categories" value="1" <?php echo ($consider_categories) ? 'checked="checked"' : '' ?>/><?php echo $text_yes ?>
            </label>
            <label class="btn btn-default <?php echo (!$consider_categories) ? 'active' : '' ?>"> 
                <input type="radio" name="consider_categories" value="0" <?php echo (!$consider_categories) ? 'checked="checked"' : '' ?>/><?php echo $text_no ?>
            </label>
        </div>
    </div>
</div>
<?php } ?>

<?php if (isset($consider_manufacturers)) { ?>
<div class="form-group" data-mode="mode_rel" data-section="arg">
    <label class="col-sm-2 control-label" for="input-consider_manufacturers"><span data-toggle="tooltip" title="<?php echo $entry_consider_manufacturers_help ?>"><?php echo $entry_consider_manufacturers ?></span></label>
    <div class="col-sm-10">
        <div class="switch btn-group">
            <label class="btn btn-default <?php echo ($consider_manufacturers) ? 'active' : '' ?>"> 
                <input type="radio" name="consider_manufacturers" value="1" <?php echo ($consider_manufacturers) ? 'checked="checked"' : '' ?>/><?php echo $text_yes ?>
            </label>
            <label class="btn btn-default <?php echo (!$consider_manufacturers) ? 'active' : '' ?>"> 
                <input type="radio" name="consider_manufacturers" value="0" <?php echo (!$consider_manufacturers) ? 'checked="checked"' : '' ?>/><?php echo $text_no ?>
            </label>
        </div>
    </div>
</div>
<?php } ?>

<div class="form-group" data-section="arg">
    <label class="col-sm-2 control-label" for="input-sort"><?php echo $entry_sort_order ?></label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-6">
                <select name="sort" id="input-sort" class="form-control">
                    <?php foreach ($sorts as $sort_index => $sort_name) { ?>
                        <?php if ($sort == $sort_index) { ?>
                        <option data-group="<?php echo $group_index ?>" value="<?php echo $sort_index ?>" selected="selected"><?php echo $sort_name ?></option>
                        <?php } else { ?>
                        <option data-group="<?php echo $group_index ?>" value="<?php echo $sort_index ?>"><?php echo $sort_name ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-6">
                <select name="order" id="input-order" class="form-control">
                    <?php foreach ($orders as $order_index => $order_name) { ?>
                        <?php if ($order == $order_index) { ?>
                        <option data-group="<?php echo $group_index ?>" value="<?php echo $order_index ?>" selected="selected"><?php echo $order_name ?></option>
                        <?php } else { ?>
                        <option data-group="<?php echo $group_index ?>" value="<?php echo $order_index ?>"><?php echo $order_name ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>

<script>
function initMode(mode) {
    $('.form-group[data-mode]').addClass('hidden');
    $('.form-group[data-mode=' + mode + ']').removeClass('hidden');
}

function invertSelection(sender) {
    $.each($(sender).closest('.form-group').find('input[type=checkbox]'), function(index, value){
        $(value).prop('checked', !$(value).prop('checked'));
    });
}

$('#data_source_form').delegate('.mode > label', 'click', function(e) {
    let mode = $(this).find('input').data('group');
    initMode(mode);
});

var mode = $('input[name=mode]:checked').data('group');

initMode(mode);
</script>

<style>
#form-module .checkbox > .control-label {
    display: flex;
    align-items: center;
}
#form-module .form-group[data-section="arg"] {
	background-color: #fff4d9;
	border-left: 5px solid #ffd166;
}
</style>