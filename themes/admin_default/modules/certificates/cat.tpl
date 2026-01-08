<!-- BEGIN: main -->
<div class="row">
    <div class="col-md-24">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->
        <div class="panel panel-default">
            <div class="panel-heading">{CAPTION}</div>
            <div class="panel-body">
                <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
                    <input type="hidden" name="catid" value="{ROW.catid}" />
                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity(nv_required)" oninput="setCustomValidity('')" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.alias}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="alias" value="{ROW.alias}" placeholder="Để trống tự động tạo" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.status}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <select class="form-control" name="status">
                                <option value="1" {ROW.status_1}>{LANG.active}</option>
                                <option value="0" {ROW.status_0}>{LANG.inactive}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center"><input class="btn btn-primary" name="save" type="submit" value="{LANG.save}" /></div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center" width="50">ID</th>
                        <th>{LANG.title}</th>
                        <th>{LANG.alias}</th>
                        <th class="text-center" width="100">{LANG.status}</th>
                        <th class="text-center" width="100">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td class="text-center">{CAT.catid}</td>
                        <td>{CAT.title}</td>
                        <td>{CAT.alias}</td>
                        <td class="text-center">{CAT.status_str}</td>
                        <td class="text-center">
                            <a href="{CAT.link_edit}" class="btn btn-default btn-xs"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0);" onclick="nv_del_cat({CAT.catid})" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function nv_del_cat(catid) {
        if (confirm('{LANG.delete_confirm}')) {
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat', 'delete=1&catid=' + catid, function(res) {
                if (res == 'OK') {
                    window.location.href = window.location.href;
                } else {
                    alert(res);
                }
            });
        }
    }
</script>
<!-- END: main -->
