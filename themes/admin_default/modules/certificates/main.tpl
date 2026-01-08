<!-- BEGIN: main -->
<div class="well">
    <form action="{NV_BASE_ADMINURL}index.php" method="get">
        <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
        <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
        <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <input class="form-control" type="text" value="{Q}" name="q" maxlength="255" placeholder="{LANG.search_key}" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <select class="form-control" name="catid">
                        <option value="0">-- {LANG.cat_manage} --</option>
                        <!-- BEGIN: cat -->
                        <option value="{CAT.catid}" {CAT.selected}>{CAT.title}</option>
                        <!-- END: cat -->
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <select class="form-control" name="status">
                        <option value="-1">-- {LANG.status} --</option>
                        <option value="1" {STATUS_1}>{LANG.active}</option>
                        <option value="0" {STATUS_0}>{LANG.inactive}</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{LANG.search}" />
                </div>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>{LANG.fullname}</th>
                <th>{LANG.birthdate}</th>
                <th>{LANG.catid}</th>
                <th>{LANG.cert_number}</th>
                <th>{LANG.reg_number}</th>
                <th>{LANG.issue_date}</th>
                <th width="100" class="text-center">{LANG.status}</th>
                <th width="100" class="text-center">Chức năng</th>
            </tr>
        </thead>
        <!-- BEGIN: page -->
        <tfoot>
            <tr>
                <td class="text-center" colspan="9">{GENERATE_PAGE}</td>
            </tr>
        </tfoot>
        <!-- END: page -->
        <tbody>
            <!-- BEGIN: loop -->
            <tr>
                <td>{ROW.id}</td>
                <td>{ROW.fullname}</td>
                <td>{ROW.birthdate}</td>
                <td>{ROW.cat_title}</td>
                <td>{ROW.cert_number}</td>
                <td>{ROW.reg_number}</td>
                <td>{ROW.issue_date_str}</td>
                <td class="text-center">
                    <input type="checkbox" name="status" id="change_status_{ROW.id}" value="{ROW.id}" {CHECK} onclick="nv_change_status({ROW.id});" />
                </td>
                <td class="text-center">
                    <a href="{ROW.link_edit}" class="btn btn-default btn-xs"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" onclick="nv_del_row({ROW.id})" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
            <!-- END: loop -->
        </tbody>
    </table>
</div>

<div class="text-center margin-top">
    <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=content" class="btn btn-success"><i class="fa fa-plus-circle"></i> {LANG.add_content}</a>
</div>
<script>
    function nv_del_row(id) {
        if (confirm('{LANG.delete_confirm}')) {
             $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=del_content', 'delete=1&id=' + id, function(res) {
                if (res == 'OK') {
                    window.location.href = window.location.href;
                } else {
                    alert('Error: ' + res);
                }
            });
        }
    }

    function nv_change_status(id) {
        var new_status = $('#change_status_' + id).is(':checked') ? true : false;
        if (confirm(nv_is_change_act_confirm[0])) {
            var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=main&nocache=' + new Date().getTime(), 'change_status=1&catid='+id, function(res) {
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
</script>
<!-- END: main -->
