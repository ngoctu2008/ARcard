<!-- BEGIN: main -->
<!-- BEGIN: success_msg -->
<div class="alert alert-success">{SUCCESS_MSG}</div>
<!-- END: success_msg -->

<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={CURRENT_LANG}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.send_notification}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.noti_title}</label>
                <div class="col-sm-8">
                    <input type="text" name="title" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.noti_message}</label>
                <div class="col-sm-8">
                    <textarea name="message" class="form-control" required></textarea>
                </div>
            </div>
             <div class="form-group">
                <label class="col-sm-4 control-label">{LANG.noti_url}</label>
                <div class="col-sm-8">
                    <input type="url" name="url" class="form-control" />
                </div>
            </div>

            <div class="text-center">
                <button type="submit" name="send" value="1" class="btn btn-primary">{LANG.send}</button>
            </div>
        </div>
    </div>
</form>
<!-- END: main -->
