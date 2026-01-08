<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<div class="row">
    <div class="col-md-24">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.edit_content}</div>
            <div class="panel-body">
                <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}&id={ROW.id}" method="post">
                    <input type="hidden" name="save" value="1" />

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.catid}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <select class="form-control" name="catid">
                                <!-- BEGIN: cat -->
                                <option value="{CAT.catid}" {CAT.selected}>{CAT.title}</option>
                                <!-- END: cat -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.fullname}</strong> <span class="text-danger">(*)</span></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="fullname" value="{ROW.fullname}" required="required" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.birthdate}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="birthdate" value="{ROW.birthdate}" placeholder="dd/mm/yyyy" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.cert_number}</strong> <span class="text-danger">(*)</span></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="cert_number" value="{ROW.cert_number}" required="required" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.reg_number}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="reg_number" value="{ROW.reg_number}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.issue_date}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <div class="input-group">
                                <input class="form-control datepicker" type="text" name="issue_date" value="{ROW.issue_date}" />
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.classification}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="classification" value="{ROW.classification}" />
                        </div>
                    </div>

                    <div class="form-group" style="text-align: center">
                        <input class="btn btn-primary" type="submit" value="{LANG.save}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(".datepicker").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
    });
</script>
<!-- END: main -->
