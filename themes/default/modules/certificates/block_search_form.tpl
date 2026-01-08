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
            <button type="submit" name="search" value="1" class="btn btn-primary btn-block">{LANG.search_btn}</button>
        </form>
    </div>
</div>
<!-- END: main -->
