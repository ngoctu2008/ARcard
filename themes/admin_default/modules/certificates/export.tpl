<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-heading">{LANG.export_excel}</div>
    <div class="panel-body">
        <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="export" value="1" />

            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.select_cat_to_export}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <select class="form-control" name="catid">
                        <option value="0">--- {LANG.export_all} ---</option>
                        <!-- BEGIN: cat -->
                        <option value="{CAT.catid}">{CAT.title}</option>
                        <!-- END: cat -->
                    </select>
                </div>
            </div>

            <div class="form-group text-center">
                <button class="btn btn-primary" type="submit"><i class="fa fa-download"></i> {LANG.export_excel}</button>
            </div>
        </form>
    </div>
</div>
<!-- END: main -->
