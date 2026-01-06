<!-- BEGIN: main -->
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center" width="50">{LANG.weight}</th>
                <th>{LANG.title}</th>
                <th class="text-center">{LANG.add_time}</th>
                <th class="text-center">{LANG.edit_time}</th>
                <th class="text-center" width="150">{LANG.status}</th>
                <th class="text-center" width="150"></th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: list -->
            <tr>
                <td class="text-center">
                    <select class="form-control" id="id_weight_{ITEM.id}" onchange="nv_change_weight({ITEM.id});">
                        <!-- BEGIN: weight -->
                        <option value="{WEIGHT.key}" {WEIGHT.selected}>{WEIGHT.title}</option>
                        <!-- END: weight -->
                    </select>
                </td>
                <td><a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=content&amp;id={ITEM.id}">{ITEM.title}</a> <span class="text-muted">({ITEM.cat_title})</span></td>
                <td class="text-center">{ITEM.add_time}</td>
                <td class="text-center">{ITEM.edit_time}</td>
                <td class="text-center">
                    <select class="form-control" id="change_status_{ITEM.id}" onchange="nv_change_status({ITEM.id});">
                        <!-- BEGIN: status -->
                        <option value="{STATUS.key}" {STATUS.selected}>{STATUS.title}</option>
                        <!-- END: status -->
                    </select>
                </td>
                <td class="text-center">
                    <em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=content&amp;id={ITEM.id}">{LANG.edit}</a> -
                    <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_content({ITEM.id});">{LANG.delete}</a>
                </td>
            </tr>
            <!-- END: list -->
        </tbody>
    </table>
</div>

<script type="text/javascript">
function nv_del_content(id) {
    if (confirm('{LANG.confirm_delete}')) {
        $.post('{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}', 'delete=' + id, function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert('Error!');
            }
        });
    }
}

function nv_change_weight(id) {
    var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
    var new_vid = $('#id_weight_' + id).val();
    $.post('{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;nocache=' + new Date().getTime(), 'ajax_action=1&id=' + id + '&new_vid=' + new_vid, function(res) {
        var r_split = res.split('_');
        if (r_split[0] != 'OK') {
            alert('{LANG.error_save}');
        } else {
             window.location.href = window.location.href;
        }
    });
}

function nv_change_status(id) {
    var new_status = $('#change_status_' + id).val();
    if (confirm('{LANG.confirm_change_status}')) {
        var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
        $.post('{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;nocache=' + new Date().getTime(), 'change_status=1&id='+id + '&new_status=' + new_status, function(res) {
            var r_split = res.split('_');
            if (r_split[0] != 'OK') {
                alert('{LANG.error_save}');
            }
        });
    } else {
        window.location.href = window.location.href;
    }
}
</script>
<!-- END: main -->
