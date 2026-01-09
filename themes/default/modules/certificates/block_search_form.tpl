<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-body">
        <form action="{ACTION}" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="cert_number" placeholder="{LANG.cert_number}" required />
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="second_factor" placeholder="{LANG.second_factor}" required />
            </div>
            <!-- BEGIN: captcha -->
            <div class="form-group">
                <div class="middle text-center clearfix">
                    <img class="captchaImg display-inline-block" src="{CAPTCHA_URL}" height="40" aria-label="{LANG.captcha}" width="{GFX_NUM}" />
                    <em class="fa fa-refresh fa-lg fa-pointer display-inline-block" title="{LANG.captcha_refresh}" onclick="change_captcha('.captchaImg');"></em>
                </div>
                <input type="text" placeholder="{LANG.captcha}" maxlength="{GFX_NUM}" value="" name="captcha" class="form-control pull-left" style="margin-top: 5px" required="required" />
            </div>
            <!-- END: captcha -->
            <button type="submit" name="search" value="1" class="btn btn-primary btn-block">{LANG.search_btn}</button>
        </form>
    </div>
</div>
<!-- END: main -->
