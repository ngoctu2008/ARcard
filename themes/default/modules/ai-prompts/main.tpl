<!-- BEGIN: main -->
<div class="aiprompts-main">
    <!-- BEGIN: cat -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{CAT.title}</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <!-- BEGIN: item -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card h-100" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 15px;">
                        <div class="card-body">
                            <h4 class="card-title"><a href="{ITEM.link}">{ITEM.title}</a></h4>
                            <p class="card-text text-muted">{ITEM.description}</p>
                            <a href="{ITEM.link}" class="btn btn-primary btn-sm">{LANG.detail}</a>
                        </div>
                    </div>
                </div>
                <!-- END: item -->
            </div>
        </div>
    </div>
    <!-- END: cat -->
</div>
<!-- END: main -->
