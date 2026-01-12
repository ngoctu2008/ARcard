<!-- BEGIN: main -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={CURRENT_LANG}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.auto_news_config}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.auto_active}</label>
                <div class="col-sm-8">
                    <label><input type="checkbox" name="auto_active" value="1" {DATA.auto_active_checked} /> {LANG.auto_active}</label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.auto_modules}</label>
                <div class="col-sm-8">
                    <!-- BEGIN: module -->
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="auto_modules[]" value="{MODULE.module_file}" {MODULE.checked} />
                            {MODULE.custom_title} ({MODULE.module_file})
                        </label>
                    </div>
                    <!-- END: module -->
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" name="save" value="1" class="btn btn-primary">{LANG.save}</button>
    </div>
</form>
<!-- END: main -->
