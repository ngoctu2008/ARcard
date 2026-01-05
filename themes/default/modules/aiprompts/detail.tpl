<!-- BEGIN: main -->
<div class="aiprompts-detail">
    <h1 class="mb-3">{ROW.title}</h1>
    <p class="text-muted">{ROW.description}</p>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">{LANG.input_form_title}</div>
                <div class="panel-body">
                    <form id="ai-form" onsubmit="return false;">
                        <!-- BEGIN: input_text -->
                        <div class="form-group">
                            <label>{CONF.label} {CONF.required_star}</label>
                            <input type="text" class="form-control" name="{CONF.key}" {CONF.required_attr} />
                        </div>
                        <!-- END: input_text -->

                        <!-- BEGIN: input_number -->
                        <div class="form-group">
                            <label>{CONF.label} {CONF.required_star}</label>
                            <input type="number" class="form-control" name="{CONF.key}" {CONF.required_attr} />
                        </div>
                        <!-- END: input_number -->

                        <!-- BEGIN: input_textarea -->
                        <div class="form-group">
                            <label>{CONF.label} {CONF.required_star}</label>
                            <textarea class="form-control" name="{CONF.key}" rows="3" {CONF.required_attr}></textarea>
                        </div>
                        <!-- END: input_textarea -->

                        <!-- BEGIN: input_select -->
                        <div class="form-group">
                            <label>{CONF.label} {CONF.required_star}</label>
                            <select class="form-control" name="{CONF.key}" {CONF.required_attr}>
                                <option value="">--- Chọn {CONF.label} ---</option>
                                <!-- BEGIN: option -->
                                <option value="{OPTION.value}">{OPTION.title}</option>
                                <!-- END: option -->
                            </select>
                        </div>
                        <!-- END: input_select -->

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-primary btn-lg" onclick="generatePrompt()"><i class="fa fa-magic"></i> {LANG.generate_prompt}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
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
</div>

<div id="raw-prompt" style="display:none;">{PROMPT_BODY}</div>

<script type="text/javascript">
function generatePrompt() {
    // Basic validation
    var valid = true;
    $('#ai-form [required]').each(function() {
        if ($(this).val() === '') {
            $(this).parent().addClass('has-error');
            valid = false;
        } else {
            $(this).parent().removeClass('has-error');
        }
    });

    if (!valid) {
        alert('{LANG.error_required}');
        return;
    }

    // Get Raw Prompt
    var prompt = $('#raw-prompt').text();

    // Replace keys
    // We iterate over all inputs in the form
    var formData = $('#ai-form').serializeArray();

    // First, simple replacement
    for (var i = 0; i < formData.length; i++) {
        var key = formData[i].name;
        var value = formData[i].value;
        // Regex to replace all occurrences of {key}
        var regex = new RegExp('{' + key + '}', 'g');
        prompt = prompt.replace(regex, value);
    }

    // Handle remaining placeholders? Optional: Clean them up or leave them.
    // For now, we leave them or users might see what's missing.

    $('#result-area').val(prompt);
}

function copyResult() {
    var copyText = document.getElementById("result-area");
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    document.execCommand("copy");

    $('#copy-msg').fadeIn().delay(2000).fadeOut();
}
</script>
<!-- END: main -->
