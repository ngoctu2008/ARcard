<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-heading">{LANG.config}</div>
    <div class="panel-body">
        <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>Số lượng hiển thị trên 1 trang</strong></label>
                <div class="col-sm-19 col-md-20">
                    <input class="form-control" type="number" name="per_page" value="{DATA.per_page}" required />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.config_who_view}</strong></label>
                <div class="col-sm-19 col-md-20" style="height: 200px; overflow: scroll; border: solid 1px #ddd; padding: 10px;">
                    <!-- BEGIN: group -->
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="who_view[]" value="{GROUP.id}" {GROUP.checked}> {GROUP.title}
                        </label>
                    </div>
                    <!-- END: group -->
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.config_active_captcha}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <select class="form-control" name="active_captcha">
                        <option value="0" {CAPTCHA_0}>{LANG.captcha_0}</option>
                        <option value="1" {CAPTCHA_1}>{LANG.captcha_1}</option>
                    </select>
                </div>
            </div>

            <div class="form-group text-center">
                <input class="btn btn-primary" type="submit" name="save" value="{LANG.save}" />
            </div>
        </form>
    </div>
</div>
<!-- END: main -->
