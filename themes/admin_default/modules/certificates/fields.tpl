<!-- BEGIN: main -->
<!-- BEGIN: view -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w100">{LANG.weight}</th>
                    <th>{LANG.title}</th>
                    <th>Mã trường</th>
                    <th>Loại</th>
                    <th class="w100 text-center">{LANG.active}</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td>
                        <select class="form-control" id="id_weight_{VIEW.fid}" onchange="nv_change_weight('{VIEW.fid}');">
                        <!-- BEGIN: weight_loop -->
                            <option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
                        <!-- END: weight_loop -->
                    </select>
                </td>
                    <td> {VIEW.title} </td>
                    <td> {VIEW.field} </td>
                    <td> {VIEW.field_type} </td>
                    <td class="text-center"><input type="checkbox" name="status" id="change_status_{VIEW.fid}" value="{VIEW.fid}" {CHECK} onclick="nv_change_status({VIEW.fid});" /></td>
                    <td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}#edit">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<!-- END: view -->

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<div class="panel panel-default" id="edit">
<div class="panel-heading">{CAPTION}</div>
<div class="panel-body">
<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <input type="hidden" name="fid" value="{ROW.fid}" />

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Tiêu đề</strong> <span class="text-danger">(*)</span></label>
        <div class="col-sm-19 col-md-20">
            <input class="form-control" type="text" name="title" value="{ROW.title}" required />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Mã trường (Field Name)</strong> <span class="text-danger">(*)</span></label>
        <div class="col-sm-19 col-md-18">
            <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" pattern="^[a-zA-Z0-9_]+$" required />
            <span class="help-block">Chỉ dùng chữ cái, số và gạch dưới (ví dụ: noi_sinh, ghi_chu)</span>
        </div>
        <div class="col-sm-4 col-md-2">
            <i class="fa fa-refresh fa-lg icon-pointer" onclick="nv_get_alias('id_alias');">&nbsp;</i>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Mô tả</strong></label>
        <div class="col-sm-19 col-md-20">
            <input class="form-control" type="text" name="description" value="{ROW.description}" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Loại dữ liệu</strong></label>
        <div class="col-sm-19 col-md-20">
            <select class="form-control" name="field_type">
                <!-- BEGIN: field_type -->
                <option value="{TYPE.key}" {TYPE.selected}>{TYPE.title}</option>
                <!-- END: field_type -->
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Các lựa chọn (cho Select)</strong></label>
        <div class="col-sm-19 col-md-20">
            <textarea class="form-control" name="field_choices" rows="3" placeholder="key|Label (Mỗi lựa chọn 1 dòng)">{ROW.field_choices}</textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Áp dụng cho loại văn bằng</strong></label>
        <div class="col-sm-19 col-md-20">
            <div class="checkbox">
                <label><input type="checkbox" name="catids[]" value="0" {ALL_CHECKED} onclick="nv_check_all_cats(this);"> <strong>Tất cả</strong></label>
            </div>
            <div class="row" style="max-height: 150px; overflow-y: scroll; border: 1px solid #ddd; padding: 5px; margin: 0;">
                <!-- BEGIN: cat_list -->
                <div class="col-sm-12">
                    <label class="checkbox-inline">
                        <input class="cat-checkbox" type="checkbox" name="catids[]" value="{CAT.catid}" {CAT.checked} onclick="nv_check_cat();"> {CAT.title}
                    </label>
                </div>
                <!-- END: cat_list -->
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-19 col-md-offset-4 col-md-20">
            <div class="checkbox">
                <label><input type="checkbox" name="required" value="1" {REQUIRED_CHECKED}> Bắt buộc nhập</label>
            </div>
        </div>
    </div>

    <div class="form-group text-center">
        <button class="btn btn-primary" type="submit" name="save" value="1">{LANG.save}</button>
    </div>
</form>
</div>
</div>

<script type="text/javascript">
//<![CDATA[
    function nv_get_alias(id) {
        var title = strip_tags($("[name='title']").val());
        if (title != '') {
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fields&nocache=' + new Date().getTime(), 'get_alias_title=' + encodeURIComponent(title), function(res) {
                $("#"+id).val(strip_tags(res));
            });
        }
        return false;
    }
    function nv_change_weight(id) {
        var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
        var new_vid = $('#id_weight_' + id).val();
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fields&nocache=' + new Date().getTime(), 'ajax_action=1&catid=' + id + '&new_vid=' + new_vid, function(res) {
            var r_split = res.split('_');
            if (r_split[0] != 'OK') {
                alert(nv_is_change_act_confirm[2]);
            }
            window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fields';
            return;
        });
        return;
    }


    function nv_change_status(id) {
        var new_status = $('#change_status_' + id).is(':checked') ? true : false;
        if (confirm(nv_is_change_act_confirm[0])) {
            var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fields&nocache=' + new Date().getTime(), 'change_status=1&catid='+id, function(res) {
                var r_split = res.split('_');
                if (r_split[0] != 'OK') {
                    alert(nv_is_change_act_confirm[2]);
                }
            });
        }
        else{
            $('#change_status_' + id).prop('checked', new_status ? false : true);
        }
        return;
    }
//]]>
</script>

<!-- BEGIN: auto_get_alias -->
<script type="text/javascript">
//<![CDATA[
    $("[name='title']").change(function() {
        nv_get_alias('id_alias');
    });
//]]>
</script>
<!-- END: auto_get_alias -->
<script>
    function nv_check_all_cats(el) {
        $('.cat-checkbox').prop('checked', el.checked);
    }
    function nv_check_cat() {
        if ($('.cat-checkbox:checked').length == $('.cat-checkbox').length) {
            $('input[name="catids[]"][value="0"]').prop('checked', true);
        } else {
            $('input[name="catids[]"][value="0"]').prop('checked', false);
        }
    }
</script>
<!-- END: main -->
