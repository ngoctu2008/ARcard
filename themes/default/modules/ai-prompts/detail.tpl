<!-- BEGIN: main -->
<style>
/* Custom Style for Professional Tool */
.aiprompts-detail .nav-tabs {
    border-bottom: 2px solid #009688;
    display: flex;
    flex-wrap: wrap;
}
.aiprompts-detail .nav-tabs > li {
    float: none;
    display: inline-block;
    margin-bottom: -2px;
}
.aiprompts-detail .nav-tabs > li > a {
    color: #555;
    font-weight: 600;
    border-radius: 4px 4px 0 0;
    margin-right: 2px;
    border: 1px solid transparent;
    padding: 10px 15px;
}
.aiprompts-detail .nav-tabs > li > a:hover {
    background-color: #eee;
    border-color: #eee #eee #ddd;
}
.aiprompts-detail .nav-tabs > li.active > a,
.aiprompts-detail .nav-tabs > li.active > a:focus,
.aiprompts-detail .nav-tabs > li.active > a:hover {
    color: #fff;
    cursor: default;
    background-color: #009688;
    border: 1px solid #009688;
    border-bottom-color: transparent;
}
.section-header {
    background-color: #009688;
    color: #fff;
    padding: 10px 15px;
    font-weight: bold;
    text-transform: uppercase;
    margin-top: 20px;
    margin-bottom: 10px;
    border-radius: 4px;
}
.panel-group .panel {
    margin-bottom: 5px;
    border-radius: 0;
    border: none;
    box-shadow: none;
    border-bottom: 1px solid #eee;
}
.panel-default > .panel-heading {
    background-color: #fff;
    border: none;
    padding: 10px 15px;
}
.panel-title > a {
    display: block;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}
.panel-title > a:hover {
    color: #009688;
}
.panel-title i {
    color: #666; /* Purple/Blue in image? */
    margin-right: 10px;
    width: 20px;
    text-align: center;
}
/* Checkbox styling */
.checkbox label, .radio label {
    padding-left: 5px;
}
</style>

<div class="aiprompts-detail">
    <div class="row">
        <div class="col-md-24">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-3">
                <!-- BEGIN: tab -->
                <li role="presentation" class="{TAB.active}"><a href="{TAB.link}">{TAB.title}</a></li>
                <!-- END: tab -->
            </ul>
        </div>
    </div>

    <form id="ai-form" onsubmit="return false;">
        <!-- BEGIN: section -->
        <!-- BEGIN: has_label -->
        <div class="section-header"><i class="fa fa-cogs"></i> {SEC.label}</div>
        <!-- END: has_label -->

        <div class="panel-group" id="accordion-{SEC.index}">
            <!-- BEGIN: group -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse-{GRP.index}" aria-expanded="true">
                           <i class="fa {GRP.icon}" style="{GRP.has_icon}"></i> {GRP.label}
                           <span class="pull-right"><i class="fa fa-angle-down"></i></span>
                        </a>
                    </h4>
                </div>
                <div id="collapse-{GRP.index}" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <!-- BEGIN: input -->

                        <!-- BEGIN: text -->
                        <div class="form-group">
                            <label>{INP.label} {INP.required_star}</label>
                            <input type="text" class="form-control" name="{INP.key}" {INP.required_attr} />
                        </div>
                        <!-- END: text -->

                        <!-- BEGIN: number -->
                        <div class="form-group">
                            <label>{INP.label} {INP.required_star}</label>
                            <input type="number" class="form-control" name="{INP.key}" {INP.required_attr} />
                        </div>
                        <!-- END: number -->

                        <!-- BEGIN: textarea -->
                        <div class="form-group">
                            <label>{INP.label} {INP.required_star}</label>
                            <textarea class="form-control" name="{INP.key}" rows="3" {INP.required_attr}></textarea>
                        </div>
                        <!-- END: textarea -->

                        <!-- BEGIN: select -->
                        <div class="form-group">
                            <label>{INP.label} {INP.required_star}</label>
                            <select class="form-control" name="{INP.key}" {INP.required_attr}>
                                <option value="">--- Chọn {INP.label} ---</option>
                                <!-- BEGIN: option -->
                                <option value="{OPT.value}">{OPT.title}</option>
                                <!-- END: option -->
                            </select>
                        </div>
                        <!-- END: select -->

                        <!-- BEGIN: checkbox -->
                        <div class="form-group">
                             <label>{INP.label} {INP.required_star}</label>
                             <div class="checkbox-list">
                                 <!-- BEGIN: option -->
                                 <div class="checkbox">
                                     <label>
                                         <input type="checkbox" name="{INP.key}[]" value="{OPT.value}"> {OPT.title}
                                     </label>
                                 </div>
                                 <!-- END: option -->
                             </div>
                        </div>
                        <!-- END: checkbox -->

                        <!-- BEGIN: radio -->
                        <div class="form-group">
                             <label>{INP.label} {INP.required_star}</label>
                             <div class="radio-list">
                                 <!-- BEGIN: option -->
                                 <div class="radio">
                                     <label>
                                         <input type="radio" name="{INP.key}" value="{OPT.value}"> {OPT.title}
                                     </label>
                                 </div>
                                 <!-- END: option -->
                             </div>
                        </div>
                        <!-- END: radio -->

                        <!-- END: input -->
                    </div>
                </div>
            </div>
            <!-- END: group -->
        </div>
        <!-- END: section -->

        <div class="text-center mt-3" style="margin-top: 20px; margin-bottom: 50px;">
            <button type="button" class="btn btn-success btn-lg" style="background-color: #009688; border-color: #009688;" onclick="generatePrompt()"><i class="fa fa-magic"></i> {LANG.generate_prompt}</button>
            <button type="button" class="btn btn-default btn-lg" onclick="resetForm()"><i class="fa fa-refresh"></i> Làm mới</button>
        </div>
    </form>

    <!-- Result Area -->
    <div id="result-container" style="display:none; margin-top: 30px;">
         <div class="panel panel-success">
            <div class="panel-heading">{LANG.result_prompt}</div>
            <div class="panel-body">
                <textarea id="result-area" class="form-control" rows="15" readonly="readonly" style="background-color: #f9f9f9; cursor: text;"></textarea>
                <div class="text-center mt-3">
                     <button type="button" class="btn btn-success" onclick="copyResult()"><i class="fa fa-copy"></i> {LANG.copy_prompt}</button>
                     <span id="copy-msg" class="text-success" style="display:none; margin-left: 10px;">{LANG.copied}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="raw-prompt" style="display:none;">{PROMPT_BODY}</div>

<script type="text/javascript">
// Initialize uniqueness for collapse IDs if multiple sections
$(document).ready(function(){
    // Basic JS to handle toggle icons if needed
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find(".fa-angle-right").removeClass("fa-angle-right").addClass("fa-angle-down");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-right");
    });
});

function resetForm() {
    document.getElementById("ai-form").reset();
    $('#result-container').hide();
}

function generatePrompt() {
    // Basic validation
    var valid = true;
    $('#ai-form [required]').each(function() {
        if ($(this).val() === '') {
            $(this).closest('.form-group').addClass('has-error');
            valid = false;
        } else {
            $(this).closest('.form-group').removeClass('has-error');
        }
    });

    if (!valid) {
        alert('{LANG.error_required}');
        return;
    }

    // Get Raw Prompt
    var prompt = $('#raw-prompt').text();

    // Get form data
    var formData = $('#ai-form').serializeArray();

    // Convert to object for easier handling
    var dataObj = {};
    for (var i = 0; i < formData.length; i++) {
        var item = formData[i];
        if (dataObj[item.name]) {
            // If already exists (checkbox array), append
            if (typeof dataObj[item.name] === 'string') {
                dataObj[item.name] = [dataObj[item.name]];
            }
            dataObj[item.name].push(item.value);
        } else {
            dataObj[item.name] = item.value;
        }
    }

    // Replace keys
    for (var key in dataObj) {
        var value = dataObj[key];
        // Handle array (checkbox) - join with comma
        if (Array.isArray(value)) {
            value = value.join(', ');
        }

        // Regex to replace all occurrences of {key}
        // Checkboxes name often has [] but the key in prompt is just name
        // e.g. name="purpose[]" -> key "purpose"
        var cleanKey = key.replace('[]', '');
        var regex = new RegExp('\\{' + cleanKey + '\\}', 'g');
        prompt = prompt.replace(regex, value);
    }

    // Clean up unused placeholders?
    // For now, simple replacement is safe.

    $('#result-area').val(prompt);
    $('#result-container').show();
    // Scroll to result
    $('html, body').animate({
        scrollTop: $("#result-container").offset().top
    }, 500);
}

function copyResult() {
    var copyText = document.getElementById("result-area");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");

    $('#copy-msg').fadeIn().delay(2000).fadeOut();
}
</script>
<!-- END: main -->
