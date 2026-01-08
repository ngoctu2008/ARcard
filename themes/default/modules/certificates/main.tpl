<!-- BEGIN: main -->
<div class="container-fluid certificates-module">
    <div class="row justify-content-center">
        <div class="col-md-16 offset-md-4 col-sm-24">

            <h2 class="text-center text-uppercase margin-bottom-lg">{LANG.search}</h2>

            <!-- BEGIN: error -->
            <div class="alert alert-danger text-center">{ERROR}</div>
            <!-- END: error -->

            <!-- BEGIN: form -->
            <div class="card search-panel border-primary">
                <div class="card-body">
                    <form action="{ACTION}" method="post" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-md-6 col-form-label text-md-right">{LANG.cert_number} <span class="text-danger">(*)</span></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="cert_number" required placeholder="Nhập số hiệu văn bằng hoặc số vào sổ..." />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-form-label text-md-right">{LANG.second_factor} <span class="text-danger">(*)</span></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="second_factor" required placeholder="Họ tên hoặc Ngày sinh (dd/mm/yyyy)..." />
                            </div>
                        </div>

                        <!-- BEGIN: captcha -->
                        <div class="form-group row">
                            <label class="col-md-6 col-form-label text-md-right">{LANG.captcha} <span class="text-danger">(*)</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="captcha" maxlength="{GFX_NUM}" required />
                            </div>
                            <div class="col-md-6">
                                <img class="captchaImg" src="{CAPTCHA_URL}" height="32" width="100" alt="Captcha" onclick="change_captcha(this);" data-src="{CAPTCHA_URL}"/>
                            </div>
                        </div>
                        <!-- END: captcha -->

                        <div class="form-group row text-center">
                            <div class="col-md-24">
                                <button type="submit" name="search" value="1" class="btn btn-primary btn-lg"><i class="fa fa-search"></i> {LANG.search_btn}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END: form -->

            <!-- BEGIN: result_box -->
            <div id="print-area">
                <!-- BEGIN: loop -->
                <div class="card result-card margin-top-lg mb-4 shadow-sm" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <div class="card-header bg-success text-white" style="padding: 10px 15px; background-color: #28a745; color: white;">
                        <h4 class="card-title m-0"><i class="fa fa-graduation-cap"></i> {RESULT.fullname}</h4>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- BEGIN: image -->
                                <div class="text-center mb-3">
                                    <a href="{RESULT.image}" target="_blank">
                                        <img src="{RESULT.image}" alt="{RESULT.fullname}" class="img-thumbnail" style="max-height: 200px;" />
                                    </a>
                                </div>
                                <!-- END: image -->
                            </div>
                            <div class="col-md-16">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><strong>{LANG.birthdate}:</strong> {RESULT.birthdate}</p>
                                        <p><strong>{LANG.cat}:</strong> {RESULT.cat_title}</p>
                                        <p>
                                            <strong>{LANG.classification} / Classification:</strong>
                                            {RESULT.classification}
                                            <!-- BEGIN: class_en -->
                                            / {RESULT.classification_en}
                                            <!-- END: class_en -->
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><strong>{LANG.cert_number}:</strong> <span class="text-danger font-weight-bold">{RESULT.cert_number}</span></p>
                                        <p><strong>{LANG.reg_number}:</strong> {RESULT.reg_number}</p>
                                        <p><strong>{LANG.issue_date}:</strong> {RESULT.issue_date_str}</p>
                                    </div>
                                    <!-- BEGIN: custom_field -->
                                    <div class="col-md-12">
                                        <p><strong>{FIELD.title}:</strong> {FIELD.value}</p>
                                    </div>
                                    <!-- END: custom_field -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: loop -->
            </div>

            <div class="text-center margin-top-lg">
                <button onclick="window.print();" class="btn btn-default"><i class="fa fa-print"></i> {LANG.print}</button>
            </div>
            <!-- END: result_box -->

        </div>
    </div>
</div>
<script>
    function change_captcha(obj) {
        var src = $(obj).attr('data-src');
        $(obj).attr('src', src + '&t=' + new Date().getTime());
    }
</script>
<style>
    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .btn { display: none; }
    }
    .result-card {
        background: #fff;
    }
</style>
<!-- END: main -->
