<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/select2_{NV_LANG_INTERFACE}.js"></script>
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">

<div class="row">
    <div class="col-md-24">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.edit_content}</div>
            <div class="panel-body">
                <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}&id={ROW.id}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="save" value="1" />

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.catid}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <select class="form-control" name="catid" id="id_catid" onchange="filter_custom_fields();">
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
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.classification} (VN)</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="classification" value="{ROW.classification}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Classification (EN)</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="classification_en" value="{ROW.classification_en}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Hình ảnh</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <div class="input-group">
                                <input class="form-control" type="text" name="image" value="{ROW.image}" id="id_image" />
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" onclick="nv_open_browse( '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}=upload&popup=1&area=id_image&path={UPLOADS_DIR_USER}&type=image', 'NVImg', 850, 420, 'resizable=no,scrollbars=no,toolbar=no,location=no,status=no' ); return false; "><i class="fa fa-folder-open-o"></i> Browse...</button>
                                </span>
                            </div>
                            <div class="help-block">Hoặc upload file trực tiếp:</div>
                            <input type="file" name="image_file" class="form-control" accept="image/*" />
                        </div>
                    </div>

                    <!-- BEGIN: field -->
                    <div class="form-group custom-field-row" data-catids="{FIELD.data_catids}">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{FIELD.title}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <!-- BEGIN: textbox -->
                            <input class="form-control" type="text" name="{FIELD.field}" value="{FIELD.value}" {FIELD.required_attr} />
                            <!-- END: textbox -->
                            <!-- BEGIN: select -->
                            <select class="form-control" name="{FIELD.field}">
                                <!-- BEGIN: option -->
                                <option value="{OPTION.key}" {OPTION.selected}>{OPTION.title}</option>
                                <!-- END: option -->
                            </select>
                            <!-- END: select -->
                            <span class="help-block">{FIELD.description}</span>
                        </div>
                    </div>
                    <!-- END: field -->

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

    function filter_custom_fields() {
        var catid = $('#id_catid').val();
        $('.custom-field-row').each(function() {
            var catids = $(this).data('catids');
            if (catids == '0' || catids == 0) {
                $(this).show();
            } else {
                var arr = catids.toString().split(',');
                if (arr.includes(catid)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            }
        });
    }

    // Run on load
    $(document).ready(function() {
        filter_custom_fields();
    });
</script>
<!-- END: main -->
