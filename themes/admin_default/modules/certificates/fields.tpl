<!-- BEGIN: main -->
<div class="row">
    <div class="col-md-24">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->

        <div class="panel panel-default">
            <div class="panel-heading">{CAPTION}</div>
            <div class="panel-body">
                <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}={OP}" method="post">
                    <input type="hidden" name="fid" value="{FORM.fid}" />

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Mã trường (Field Name)</strong> <span class="text-danger">(*)</span></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="field" value="{FORM.field}" pattern="^[a-zA-Z0-9_]+$" required />
                            <span class="help-block">Chỉ dùng chữ cái, số và gạch dưới (ví dụ: noi_sinh, ghi_chu)</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Tiêu đề</strong> <span class="text-danger">(*)</span></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="title" value="{FORM.title}" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Mô tả</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <input class="form-control" type="text" name="description" value="{FORM.description}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Loại dữ liệu</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <select class="form-control" name="field_type">
                                <!-- BEGIN: field_type -->
                                <option value="{TYPE.key}" {TYPE.selected}>{TYPE.title}</option>
                                <!-- END: field_type -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 col-md-4 control-label"><strong>Các lựa chọn (cho Select)</strong></label>
                        <div class="col-sm-19 col-md-20">
                            <textarea class="form-control" name="field_choices" rows="3" placeholder="key|Label (Mỗi lựa chọn 1 dòng)">{FORM.field_choices}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-5 col-sm-19 col-md-offset-4 col-md-20">
                            <div class="checkbox">
                                <label><input type="checkbox" name="required" value="1" {REQUIRED_CHECKED}> Bắt buộc nhập</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button class="btn btn-primary" type="submit" name="save" value="1">{LANG.save}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Mã trường</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th width="100" class="text-center">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td>{ROW.fid}</td>
                        <td>{ROW.field}</td>
                        <td>{ROW.title}</td>
                        <td>{ROW.field_type}</td>
                        <td class="text-center">
                            <a href="{ROW.link_edit}" class="btn btn-default btn-xs"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0);" onclick="nv_del_field({ROW.fid})" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function nv_del_field(fid) {
        if (confirm('{LANG.delete_confirm}')) {
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fields', 'delete=1&fid=' + fid, function(res) {
                if (res == 'OK') {
                    window.location.href = window.location.href;
                } else {
                    alert('Error: ' + res);
                }
            });
        }
    }
</script>
<!-- END: main -->
