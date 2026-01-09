<!-- BEGIN: main -->
<div class="row">
    <div class="col-md-24">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->
        <!-- BEGIN: info -->
        <div class="alert alert-success">{INFO}</div>
        <!-- END: info -->

        <div class="panel panel-default">
            <div class="panel-heading">{LANG.import_step1}</div>
            <div class="panel-body">
                <div class="alert alert-info">
                    {LANG.import_note}<br/>
                    <a id="btn-download-sample" href="#" class="btn btn-warning btn-xs disabled" target="_blank"><i class="fa fa-download"></i> Tải file mẫu (.xlsx)</a>
                </div>
                <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.catid}</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <select class="form-control" name="catid" id="id_catid" required onchange="update_import_ui();">
                                <option value="">-- Chọn loại văn bằng --</option>
                                <!-- BEGIN: cat -->
                                <option value="{CAT.catid}">{CAT.title}</option>
                                <!-- END: cat -->
                            </select>
                        </div>
                    </div>
                    <div id="div-upload-file" style="display: none;">
                        <div class="form-group">
                            <label class="col-sm-5 col-md-4 control-label"><strong>File Excel (.xlsx)</strong></label>
                            <div class="col-sm-19 col-md-20">
                                <input type="file" name="import_file" class="form-control" accept=".xlsx" required />
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <input class="btn btn-primary" type="submit" value="{LANG.preview}" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- BEGIN: preview -->
        <div class="panel panel-primary">
            <div class="panel-heading">{LANG.import_step2}</div>
            <div class="panel-body">
                <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
                    <input type="hidden" name="import" value="1" />
                    <input type="hidden" name="catid" value="{CATID}" />

                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">{LANG.import_ignore_err}</th>
                                    <th>#</th>
                                    <th>{LANG.status}</th>
                                    <th>{LANG.fullname}</th>
                                    <th>{LANG.birthdate}</th>
                                    <th>{LANG.cert_number}</th>
                                    <th>{LANG.reg_number}</th>
                                    <th>{LANG.issue_date}</th>
                                    <th>{LANG.classification}</th>
                                    <th>Custom Fields</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- BEGIN: loop -->
                                <tr class="{ITEM.status_class}">
                                    <td class="text-center">
                                        <input type="checkbox" name="import_flags[{ITEM.index}]" value="1" {ITEM.checked} />
                                    </td>
                                    <td>{ITEM.index}</td>
                                    <td>
                                        <span data-toggle="tooltip" title="{ITEM.warning}">{ITEM.status_text}</span>
                                    </td>
                                    <td>
                                        {ITEM.fullname}
                                        <input type="hidden" name="rows[{ITEM.index}][fullname]" value="{ITEM.fullname}">
                                    </td>
                                    <td>
                                        {ITEM.birthdate}
                                        <input type="hidden" name="rows[{ITEM.index}][birthdate]" value="{ITEM.birthdate}">
                                    </td>
                                    <td>
                                        {ITEM.cert_number}
                                        <input type="hidden" name="rows[{ITEM.index}][cert_number]" value="{ITEM.cert_number}">
                                    </td>
                                    <td>
                                        {ITEM.reg_number}
                                        <input type="hidden" name="rows[{ITEM.index}][reg_number]" value="{ITEM.reg_number}">
                                    </td>
                                    <td>
                                        {ITEM.issue_date}
                                        <input type="hidden" name="rows[{ITEM.index}][issue_date]" value="{ITEM.issue_date}">
                                    </td>
                                    <td>
                                        {ITEM.classification}
                                        <input type="hidden" name="rows[{ITEM.index}][classification]" value="{ITEM.classification}">
                                        <input type="hidden" name="rows[{ITEM.index}][classification_en]" value="{ITEM.classification_en}">
                                    </td>
                                    <!-- BEGIN: custom_field -->
                                    <td>
                                        {C_FIELD.val}
                                        <input type="hidden" name="rows[{C_FIELD.i}][custom][{C_FIELD.key}]" value="{C_FIELD.val}">
                                    </td>
                                    <!-- END: custom_field -->
                                </tr>
                                <!-- END: loop -->
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center margin-top">
                        <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-upload"></i> {LANG.import_execute}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- END: preview -->
    </div>
</div>
<script>
    var base_download_url = '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=download_sample';

    function update_import_ui() {
        var catid = $('#id_catid').val();
        if (catid && catid != 0) {
            $('#div-upload-file').show();
            $('#btn-download-sample').removeClass('disabled');
            $('#btn-download-sample').attr('href', base_download_url + '&catid=' + catid);
        } else {
            $('#div-upload-file').hide();
            $('#btn-download-sample').addClass('disabled');
            $('#btn-download-sample').attr('href', '#');
        }
    }

    $(document).ready(function() {
        update_import_ui();
    });
</script>
<!-- END: main -->
