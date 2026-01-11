<!-- BEGIN: main -->
<div class="alert alert-info">{LANG.icon_note}</div>

<!-- BEGIN: success_msg -->
<div class="alert alert-success">{SUCCESS_MSG}</div>
<!-- END: success_msg -->

<!-- BEGIN: error_msg -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error_msg -->

<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={CURRENT_LANG}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.manifest_name}</label>
                <div class="col-sm-8">
                    <input type="text" name="manifest_name" value="{DATA.manifest_name}" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.manifest_short_name}</label>
                <div class="col-sm-8">
                    <input type="text" name="manifest_short_name" value="{DATA.manifest_short_name}" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.theme_color}</label>
                <div class="col-sm-8">
                    <input type="color" name="theme_color" value="{DATA.theme_color}" class="form-control" style="height: 40px; width: 60px; padding: 2px" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.background_color}</label>
                <div class="col-sm-8">
                    <input type="color" name="background_color" value="{DATA.background_color}" class="form-control" style="height: 40px; width: 60px; padding: 2px" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.icon}</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="icon_path" id="icon_path" value="{DATA.icon_path}" placeholder="{LANG.icon_note}" />
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="select_icon">
                                <em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">VAPID Keys (Push Notifications)</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.vapid_public_key}</label>
                <div class="col-sm-8">
                    <input type="text" name="vapid_public_key" value="{DATA.vapid_public_key}" class="form-control" readonly />
                </div>
            </div>
             <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.vapid_private_key}</label>
                <div class="col-sm-8">
                    <input type="text" name="vapid_private_key" value="{DATA.vapid_private_key}" class="form-control" readonly />
                </div>
            </div>

            <!-- BEGIN: generate_keys -->
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" name="generate_keys" value="1" class="btn btn-warning">Generate VAPID Keys</button>
                </div>
            </div>
            <!-- END: generate_keys -->
        </div>
    </div>

    <div class="text-center" style="margin-bottom: 20px;">
        <button type="submit" name="save" value="1" class="btn btn-primary btn-lg">{LANG.save}</button>
    </div>
</form>

<script type="text/javascript">
$(document).ready(function() {
    $('#select_icon').click(function() {
        var area = "icon_path";
        var path = "{NV_UPLOADS_DIR}";
        var currentpath = "{NV_UPLOADS_DIR}";
        var type = "image";
        nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
        return false;
    });
});
</script>
<!-- END: main -->
