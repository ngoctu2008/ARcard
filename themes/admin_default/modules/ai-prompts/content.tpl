<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->

<div class="panel panel-default">
    <div class="panel-heading">{LANG.template_manage}</div>
    <div class="panel-body">
        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="id" value="{ROW.id}" />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">{LANG.title} <span class="red">(*)</span></label>
                        <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" onchange="nv_get_alias('id_alias');" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">{LANG.catid} <span class="red">(*)</span></label>
                        <select class="form-control" name="catid">
                            <option value="0">-- Select Category --</option>
                            <!-- BEGIN: cat -->
                            <option value="{CAT.catid}" {CAT.selected}>{CAT.title}</option>
                            <!-- END: cat -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">{LANG.alias}</label>
                <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" />
            </div>
            <div class="form-group">
                <label class="control-label">{LANG.description}</label>
                <textarea class="form-control" name="description" rows="3">{ROW.description}</textarea>
            </div>

            <hr />
            <h4>{LANG.input_config}</h4>
            <div id="input-config-container">
                <!-- BEGIN: config_row -->
                <div class="config-row well well-sm">
                    <div class="row">
                        <div class="col-md-3">
                             <label>{LANG.input_label}</label>
                             <input type="text" class="form-control input-sm" name="input_label[]" value="{CONF.label}" placeholder="Label" />
                        </div>
                        <div class="col-md-2">
                             <label>{LANG.input_key}</label>
                             <input type="text" class="form-control input-sm" name="input_key[]" value="{CONF.key}" placeholder="Key (e.g. subject)" />
                        </div>
                        <div class="col-md-2">
                             <label>{LANG.input_type}</label>
                             <select class="form-control input-sm" name="input_type[]">
                                 <option value="text" {CONF.sel_text}>Text</option>
                                 <option value="textarea" {CONF.sel_textarea}>Textarea</option>
                                 <option value="number" {CONF.sel_number}>Number</option>
                                 <option value="select" {CONF.sel_select}>Select</option>
                                 <option value="checkbox" {CONF.sel_checkbox}>Checkbox</option>
                                 <option value="radio" {CONF.sel_radio}>Radio</option>
                                 <option value="section" {CONF.sel_section}>-- Section Header --</option>
                                 <option value="group" {CONF.sel_group}>-- Group/Accordion --</option>
                             </select>
                             <input type="text" class="form-control input-sm mt-1" name="input_icon[]" value="{CONF.icon}" placeholder="Icon (fa-users)" style="margin-top:5px" />
                        </div>
                        <div class="col-md-3">
                             <label>{LANG.input_options}</label>
                             <textarea class="form-control input-sm" name="input_options[]" rows="1" placeholder="Line separated options">{CONF.options_text}</textarea>
                        </div>
                         <div class="col-md-1">
                             <label>{LANG.input_required}</label><br>
                             <input type="checkbox" name="input_required[]" value="1" {CONF.checked_required} />
                        </div>
                        <div class="col-md-1 text-center">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this);"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <!-- END: config_row -->
            </div>
            <button type="button" class="btn btn-success btn-sm" onclick="addRow();"><i class="fa fa-plus"></i> {LANG.add_field}</button>

            <hr />
            <div class="form-group">
                <label class="control-label">{LANG.prompt_body}</label>
                <textarea class="form-control" name="prompt_body" rows="10">{ROW.prompt_body}</textarea>
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
                <th>{LANG.catid}</th>
                <th class="text-center" width="100">{LANG.status}</th>
                <th class="text-center" width="150"></th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: list -->
            <tr>
                <td class="text-center">{ITEM.id}</td>
                <td><a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;id={ITEM.id}">{ITEM.title}</a></td>
                <td>{ITEM.cat_title}</td>
                <td class="text-center">{ITEM.status}</td>
                <td class="text-center">
                    <em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;id={ITEM.id}">{LANG.edit}</a> -
                    <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_content({ITEM.id});">{LANG.delete}</a>
                </td>
            </tr>
            <!-- END: list -->
        </tbody>
    </table>
</div>

<script type="text/javascript">
function addRow() {
    var html = '<div class="config-row well well-sm"><div class="row">';
    html += '<div class="col-md-3"><input type="text" class="form-control input-sm" name="input_label[]" placeholder="Label" /></div>';
    html += '<div class="col-md-2"><input type="text" class="form-control input-sm" name="input_key[]" placeholder="Key" /></div>';
    html += '<div class="col-md-2"><select class="form-control input-sm" name="input_type[]"><option value="text">Text</option><option value="textarea">Textarea</option><option value="number">Number</option><option value="select">Select</option><option value="checkbox">Checkbox</option><option value="radio">Radio</option><option value="section">-- Section Header --</option><option value="group">-- Group/Accordion --</option></select><input type="text" class="form-control input-sm mt-1" name="input_icon[]" placeholder="Icon (fa-users)" style="margin-top:5px" /></div>';
    html += '<div class="col-md-3"><textarea class="form-control input-sm" name="input_options[]" rows="1" placeholder="Options"></textarea></div>';
    html += '<div class="col-md-1"><input type="checkbox" name="input_required[]" value="1" /></div>';
    html += '<div class="col-md-1 text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this);"><i class="fa fa-trash"></i></button></div>';
    html += '</div></div>';
    $('#input-config-container').append(html);
}

function removeRow(btn) {
    $(btn).closest('.config-row').remove();
}

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
</script>
<!-- END: main -->
