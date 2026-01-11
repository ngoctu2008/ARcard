<!-- BEGIN: main -->
<!-- BEGIN: error_msg -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error_msg -->

<div class="panel panel-default">
    <div class="panel-heading">{LANG.config}</div>
    <div class="panel-body">
        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="save" value="1">

            <div class="form-group">
                <label>{LANG.manifest_name}</label>
                <input class="form-control" type="text" name="manifest_name" value="{DATA.manifest_name}">
            </div>

            <div class="form-group">
                <label>{LANG.manifest_short_name}</label>
                <input class="form-control" type="text" name="manifest_short_name" value="{DATA.manifest_short_name}">
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label>{LANG.theme_color}</label>
                        <input class="form-control" type="color" name="theme_color" value="{DATA.theme_color}">
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label>{LANG.background_color}</label>
                        <input class="form-control" type="color" name="background_color" value="{DATA.background_color}">
                    </div>
                </div>
            </div>

             <div class="form-group">
                <label>{LANG.icon} <small class="text-muted">({LANG.icon_note})</small></label>
                <div class="input-group">
                    <input class="form-control" type="text" name="icon_path" id="icon_path" value="{DATA.icon_path}">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="select_icon">Browse</button>
                    </span>
                </div>
            </div>

            <hr>
            <h3>VAPID Keys (Push Notifications)</h3>
            <div class="alert alert-info">{LANG.vapid_manual_guide}</div>

            <div class="form-group">
                <label>{LANG.vapid_public_key}</label>
                <input class="form-control" type="text" name="vapid_public_key" value="{DATA.vapid_public_key}">
            </div>

            <div class="form-group">
                <label>{LANG.vapid_private_key}</label>
                <input class="form-control" type="text" name="vapid_private_key" value="{DATA.vapid_private_key}">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">{LANG.save}</button>
                <button type="submit" name="generate_keys" value="1" class="btn btn-warning">Generate New Keys</button>
            </div>
        </form>
    </div>
</div>
<!-- END: main -->
