<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->

<div class="panel panel-default">
    <div class="panel-heading">{LANG.cat_manage}</div>
    <div class="panel-body">
        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="catid" value="{ROW.catid}" />
            <div class="form-group">
                <label class="control-label">{LANG.title} <span class="red">(*)</span></label>
                <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" onchange="nv_get_alias('id_alias');" />
            </div>
            <div class="form-group">
                <label class="control-label">{LANG.alias}</label>
                <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" />
            </div>
            <div class="form-group">
                <label class="control-label">{LANG.description}</label>
                <textarea class="form-control" name="description">{ROW.description}</textarea>
            </div>
            <div class="form-group">
                <label class="control-label">{LANG.status}</label>
                <label><input type="checkbox" name="status" value="1" <!-- BEGIN: status_checked -->checked="checked"<!-- END: status_checked --> {ROW.status_checked} /> {LANG.active}</label>
            </div>
            <div class="text-center">
                <input class="btn btn-primary" name="save" type="submit" value="{LANG.save}" />
            </div>
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
                <th class="text-center" width="150"></th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: list -->
            <tr>
                <td class="text-center">{CAT.catid}</td>
                <td><a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;catid={CAT.catid}">{CAT.title}</a></td>
                <td>{CAT.alias}</td>
                <td class="text-center">{CAT.status}</td>
                <td class="text-center">
                    <em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;catid={CAT.catid}">{LANG.edit}</a> -
                    <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_cat({CAT.catid});">{LANG.delete}</a>
                </td>
            </tr>
            <!-- END: list -->
        </tbody>
    </table>
</div>

<script type="text/javascript">
function nv_del_cat(catid) {
    if (confirm('{LANG.confirm_delete}')) {
        $.post('{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}', 'delete=' + catid, function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert('Error!');
            }
        });
    }
}
</script>
<!-- END: main -->
