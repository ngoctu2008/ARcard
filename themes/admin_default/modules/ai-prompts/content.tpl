<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->

<div class="panel panel-default">
    <div class="panel-heading">{LANG.template_manage}</div>
    <div class="panel-body">
        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="id" value="{ROW.id}" />
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">{LANG.title} <span class="red">(*)</span></label>
                        <input class="form-control" type="text" name="title" id="id_title" value="{ROW.title}" required="required" onchange="nv_get_alias('id_alias');" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">{LANG.catid} <span class="red">(*)</span></label>
                        <select class="form-control" name="catid">
                            <option value="0">-- Select Category --</option>
                            <!-- BEGIN: cat -->
                            <option value="{CAT.catid}" {CAT.selected}>{CAT.title}</option>
                            <!-- END: cat -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">{LANG.alias}</label>
                <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" />
            </div>
            <div class="form-group">
                <label class="control-label">Icon (FontAwesome)</label>
                <input class="form-control" type="text" name="icon" value="{ROW.icon}" placeholder="fa-book" />
            </div>
            <div class="form-group">
                <label class="control-label">{LANG.description}</label>
                <textarea class="form-control" name="description" rows="3">{ROW.description}</textarea>
            </div>

            <hr />
            <h4>{LANG.input_config}</h4>
            <div id="input-config-container">
                <!-- BEGIN: config_row -->
                <div class="config-row well well-sm">
                    <div class="row">
                        <div class="col-md-6">
                             <label>{LANG.input_label}</label>
                             <input type="text" class="form-control input-sm input-label-field" name="input_label[]" value="{CONF.label}" placeholder="Label" />
                        </div>
                        <div class="col-md-4">
                             <label>{LANG.input_key}</label>
                             <input type="text" class="form-control input-sm input-key-field" name="input_key[]" value="{CONF.key}" placeholder="Key (e.g. subject)" onkeyup="updateKeyButtons()" />
                        </div>
                        <div class="col-md-4">
                             <label>{LANG.input_type}</label>
                             <select class="form-control input-sm input-type-select" name="input_type[]">
                                 <option value="text" {CONF.sel_text}>Text</option>
                                 <option value="textarea" {CONF.sel_textarea}>Textarea</option>
                                 <option value="number" {CONF.sel_number}>Number</option>
                                 <option value="select" {CONF.sel_select}>Select</option>
                                 <option value="checkbox" {CONF.sel_checkbox}>Checkbox</option>
                                 <option value="radio" {CONF.sel_radio}>Radio</option>
                                 <option value="section" {CONF.sel_section}>-- Section Header --</option>
                                 <option value="group" {CONF.sel_group}>-- Group/Accordion --</option>
                             </select>
                             <input type="text" class="form-control input-sm mt-1" name="input_icon[]" value="{CONF.icon}" placeholder="Icon (fa-users)" style="margin-top:5px" />
                        </div>
                        <div class="col-md-6">
                             <label>{LANG.input_options}</label>
                             <textarea class="form-control input-sm hidden-options" name="input_options[]" rows="1" style="display:none;">{CONF.options_text}</textarea>
                             <div class="options-builder"></div>
                        </div>
                         <div class="col-md-2">
                             <label>{LANG.input_required}</label><br>
                             <input type="checkbox" name="input_required[]" value="1" {CONF.checked_required} />
                        </div>
                        <div class="col-md-2 text-center">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this);"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <!-- END: config_row -->
            </div>
            <button type="button" class="btn btn-success btn-sm" onclick="addRow();"><i class="fa fa-plus"></i> {LANG.add_field}</button>

            <hr />
            <div class="row">
                <div class="col-md-16">
                    <div class="form-group">
                        <label class="control-label">{LANG.prompt_body}</label>
                        <textarea class="form-control" name="prompt_body" id="prompt_body" rows="15">{ROW.prompt_body}</textarea>
                    </div>
                </div>
                <div class="col-md-8">
                     <label class="control-label">Insert Variables</label>
                     <div class="panel panel-default" style="height: 330px; overflow-y: auto;">
                        <div class="panel-body" id="key-buttons-container">
                            <p class="text-muted small">Add keys in Input Config to see buttons here.</p>
                        </div>
                     </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">{LANG.status}</label>
                <label><input type="checkbox" name="status" value="1" <!-- BEGIN: status_checked -->checked="checked"<!-- END: status_checked --> {ROW.status_checked} /> {LANG.active}</label>
            </div>

            <div class="text-center">
                <input class="btn btn-primary" name="save" type="submit" value="{LANG.save}" />
            </div>
        </form>
    </div>
</div>

<!-- BEGIN: auto_get_alias -->
<script type="text/javascript">
//<![CDATA[
    $("[name='title']").change(function() {
        nv_get_alias('id_alias');
    });
//]]>
</script>
<!-- END: auto_get_alias -->

<script type="text/javascript">
$(document).ready(function() {
    // Initialize existing rows
    $('.config-row').each(function() {
        initRowBuilder($(this));
    });
    updateKeyButtons();
});

function addRow() {
    var html = '<div class="config-row well well-sm"><div class="row">';
    html += '<div class="col-md-6"><input type="text" class="form-control input-sm input-label-field" name="input_label[]" placeholder="Label" /></div>';
    html += '<div class="col-md-4"><input type="text" class="form-control input-sm input-key-field" name="input_key[]" placeholder="Key" onkeyup="updateKeyButtons()" /></div>';
    html += '<div class="col-md-4"><select class="form-control input-sm input-type-select" name="input_type[]"><option value="text">Text</option><option value="textarea">Textarea</option><option value="number">Number</option><option value="select">Select</option><option value="checkbox">Checkbox</option><option value="radio">Radio</option><option value="section">-- Section Header --</option><option value="group">-- Group/Accordion --</option></select><input type="text" class="form-control input-sm mt-1" name="input_icon[]" placeholder="Icon (fa-users)" style="margin-top:5px" /></div>';
    html += '<div class="col-md-6"><textarea class="form-control input-sm hidden-options" name="input_options[]" rows="1" style="display:none;"></textarea><div class="options-builder"></div></div>';
    html += '<div class="col-md-2"><input type="checkbox" name="input_required[]" value="1" /></div>';
    html += '<div class="col-md-2 text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this);"><i class="fa fa-trash"></i></button></div>';
    html += '</div></div>';

    var newRow = $(html).appendTo('#input-config-container');
    initRowBuilder(newRow);
}

function initRowBuilder(row) {
    var typeSelect = row.find('.input-type-select');
    var optionsTextarea = row.find('.hidden-options');
    var optionsBuilder = row.find('.options-builder');

    // Initial render
    renderOptionsUI(optionsBuilder, optionsTextarea, typeSelect.val());

    // On type change
    typeSelect.on('change', function() {
        renderOptionsUI(optionsBuilder, optionsTextarea, $(this).val());
    });

    // Also trigger key update when keys change
    row.find('.input-key-field').on('input', updateKeyButtons);
}

function renderOptionsUI(container, textarea, type) {
    // Only allow for select, checkbox, radio
    if (['select', 'checkbox', 'radio'].indexOf(type) === -1) {
        container.html('');
        container.hide();
        return;
    }
    container.show();

    var currentVal = textarea.val();
    var options = currentVal ? currentVal.split('\n') : [];

    var html = '<ul class="list-unstyled options-list" style="margin-bottom:5px;">';
    options.forEach(function(optStr) {
        if(optStr.trim() !== '') {
            var parts = optStr.split('|');
            var val = parts[0];
            var lbl = parts.length > 1 ? parts[1] : parts[0];

            html += '<li style="margin-bottom:5px;"><div class="input-group input-group-sm">';
            html += '<input type="text" class="form-control opt-value" value="'+escapeHtml(val)+'" readonly style="width:40%" title="Value">';
            html += '<input type="text" class="form-control opt-label" value="'+escapeHtml(lbl)+'" readonly style="width:60%; border-left:0;" title="Label">';
            html += '<span class="input-group-btn"><button class="btn btn-default btn-delete-opt" type="button"><i class="fa fa-trash"></i></button></span></div></li>';
        }
    });
    html += '</ul>';

    html += '<div class="row" style="margin:0 -2px;">';
    html += '<div class="col-xs-10" style="padding:0 2px;"><input type="text" class="form-control input-sm new-option-value" placeholder="Value"></div>';
    html += '<div class="col-xs-10" style="padding:0 2px;"><input type="text" class="form-control input-sm new-option-label" placeholder="Label"></div>';
    html += '<div class="col-xs-4" style="padding:0 2px;"><button class="btn btn-success btn-sm btn-block btn-add-opt" type="button"><i class="fa fa-plus"></i></button></div>';
    html += '</div>';

    container.html(html);

    container.find('.btn-delete-opt').click(function() {
        $(this).closest('li').remove();
        updateTextarea(container, textarea);
    });

    container.find('.btn-add-opt').click(function() {
        addOptionFromInput(container, textarea);
    });

    container.find('.new-option-label').keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            addOptionFromInput(container, textarea);
        }
    });
}

function addOptionFromInput(container, textarea) {
    var inputVal = container.find('.new-option-value');
    var inputLbl = container.find('.new-option-label');

    var val = inputVal.val().trim();
    var lbl = inputLbl.val().trim();

    if (val) {
        if (!lbl) lbl = val;

        var ul = container.find('ul.options-list');
        var html = '<li style="margin-bottom:5px;"><div class="input-group input-group-sm">';
        html += '<input type="text" class="form-control opt-value" value="'+escapeHtml(val)+'" readonly style="width:40%" title="Value">';
        html += '<input type="text" class="form-control opt-label" value="'+escapeHtml(lbl)+'" readonly style="width:60%; border-left:0;" title="Label">';
        html += '<span class="input-group-btn"><button class="btn btn-default btn-delete-opt" type="button"><i class="fa fa-trash"></i></button></span></div></li>';

        var li = $(html);
        ul.append(li);

        li.find('.btn-delete-opt').click(function() {
            $(this).closest('li').remove();
            updateTextarea(container, textarea);
        });

        inputVal.val('');
        inputLbl.val('');
        inputVal.focus();
        updateTextarea(container, textarea);
    }
}

function updateTextarea(container, textarea) {
    var options = [];
    container.find('ul.options-list li').each(function() {
        var val = $(this).find('.opt-value').val();
        var lbl = $(this).find('.opt-label').val();
        options.push(val + '|' + lbl);
    });
    textarea.val(options.join('\n'));
}

function escapeHtml(text) {
  var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function removeRow(btn) {
    if(confirm('{LANG.confirm_delete}')) {
        $(btn).closest('.config-row').remove();
        updateKeyButtons();
    }
}

// -----------------------------------------------------
// Key Insertion Logic
// -----------------------------------------------------

function updateKeyButtons() {
    var container = $('#key-buttons-container');
    var keys = [];
    $('.input-key-field').each(function() {
        var k = $(this).val().trim();
        if (k && k !== '') {
            keys.push(k);
        }
    });

    if (keys.length === 0) {
        container.html('<p class="text-muted small">Add keys in Input Config to see buttons here.</p>');
        return;
    }

    var html = '';
    keys.forEach(function(k) {
        html += '<button type="button" class="btn btn-info btn-xs" style="margin:2px;" onclick="insertKey(\'{'+k+'}\')">{'+k+'}</button> ';
    });
    container.html(html);
}

function insertKey(text) {
    var txtarea = document.getElementById('prompt_body');
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ) );

    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") {
        strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0,strPos);
    var back = (txtarea.value).substring(strPos,txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;

    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        range.moveStart ('character', strPos);
        range.moveEnd ('character', 0);
        range.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}

</script>
<!-- END: main -->
