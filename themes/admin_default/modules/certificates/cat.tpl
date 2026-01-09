<!-- BEGIN: main -->
<!-- BEGIN: view -->
<div class="well">
<form action="{NV_BASE_ADMINURL}index.php" method="get">
    <input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
    <div class="row">
        <div class="col-xs-24 col-md-6">
            <div class="form-group">
                <input class="form-control" type="text" value="{Q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
            </div>
        </div>
    </div>
</form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w100">{LANG.weight}</th>
                    <th>{LANG.title}</th>
                    <th>{LANG.description}</th>
                    <th class="w100 text-center">{LANG.active}</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="5">{GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td>
                        <select class="form-control" id="id_weight_{VIEW.catid}" onchange="nv_change_weight('{VIEW.catid}');">
                        <!-- BEGIN: weight_loop -->
                            <option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
                        <!-- END: weight_loop -->
                    </select>
                </td>
                    <td> {VIEW.title} </td>
                    <td> {VIEW.description} </td>
                    <td class="text-center"><input type="checkbox" name="status" id="change_status_{VIEW.catid}" value="{VIEW.catid}" {CHECK} onclick="nv_change_status({VIEW.catid});" /></td>
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
    <input type="hidden" name="catid" value="{ROW.catid}" />
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong> <span class="red">(*)</span></label>
        <div class="col-sm-19 col-md-20">
            <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity(nv_required)" oninput="setCustomValidity('')" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.alias}</strong></label>
        <div class="col-sm-19 col-md-18">
            <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" />
        </div>
        <div class="col-sm-4 col-md-2">
            <i class="fa fa-refresh fa-lg icon-pointer" onclick="nv_get_alias('id_alias');">&nbsp;</i>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Hình ảnh phôi văn bằng</strong></label>
        <div class="col-sm-19 col-md-20">
            <div class="input-group">
                <input class="form-control" type="text" name="image" value="{ROW.image}" id="id_image" />
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="open_browse_image(); return false;"><i class="fa fa-folder-open-o"></i> Browse server</button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.description}</strong></label>
        <div class="col-sm-19 col-md-20">
            <textarea class="form-control" name="description" rows="3">{ROW.description}</textarea>
        </div>
    </div>
    <div class="form-group" style="text-align: center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>
</div></div>

<script type="text/javascript">
//<![CDATA[
    function nv_get_alias(id) {
        var title = strip_tags($("[name='title']").val());
        if (title != '') {
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'get_alias_title=' + encodeURIComponent(title), function(res) {
                $("#"+id).val(strip_tags(res));
            });
        }
        return false;
    }
    function nv_change_weight(id) {
        var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
        var new_vid = $('#id_weight_' + id).val();
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'ajax_action=1&catid=' + id + '&new_vid=' + new_vid, function(res) {
            var r_split = res.split('_');
            if (r_split[0] != 'OK') {
                alert(nv_is_change_act_confirm[2]);
            }
            window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat';
            return;
        });
        return;
    }


    function nv_change_status(id) {
        var new_status = $('#change_status_' + id).is(':checked') ? true : false;
        if (confirm(nv_is_change_act_confirm[0])) {
            var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'change_status=1&catid='+id, function(res) {
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

    function open_browse_image() {
        // Open window immediately
        var w = 850;
        var h = 420;
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        var win = window.open('about:blank', 'NVImg', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

        if (win) {
            win.document.write('Loading folder...');
        }

        // Call AJAX to create folder if needed and get path
        $.post(
            '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}&ajax_create_folder=1',
            function(full_path) {
                if (full_path == 'ERROR') {
                    if (win) win.close();
                    alert("Error: Could not create upload directory. Check permissions.");
                    return;
                }
                var url = '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}=upload&popup=1&area=id_image&path=' + full_path + '&type=image';
                if (win) {
                    win.location.href = url;
                    win.focus();
                }
            }
        ).fail(function() {
             if (win) {
                 win.close();
             }
             alert("Error connection.");
        });
    }
//]]>
</script>
<!-- END: auto_get_alias -->
<!-- END: main -->
