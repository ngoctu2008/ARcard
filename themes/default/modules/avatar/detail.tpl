<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/css/avatar.css">
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&family=Be+Vietnam+Pro:wght@400;700&family=Bona+Nova:wght@400;700&family=Iwona+Display:wght@400;700&family=Santor&family=Viaoda+Libre&display=swap" rel="stylesheet">

<div class="mobile-app-container">
    <div class="avatar-app" id="avatar-app">
        <!-- Steps Navigation -->
        <div class="step-nav-container">
            <ul class="step-nav">
                <li class="completed" id="step-nav-2" onclick="setStep(2)">
                    <span class="step-icon"><i class="fa fa-upload"></i></span>
                    <span class="step-text">{LANG.step_upload_photo}</span>
                </li>
                <li id="step-nav-3" onclick="setStep(3)">
                    <span class="step-icon"><i class="fa fa-paint-brush"></i></span>
                    <span class="step-text">{LANG.step_edit}</span>
                </li>
                <li id="step-nav-4" onclick="setStep(4)">
                    <span class="step-icon"><i class="fa fa-eye"></i></span>
                    <span class="step-text">{LANG.step_preview}</span>
                </li>
            </ul>
        </div>

        <!-- Step 2: Upload Photo -->
        <div class="step-content" id="step-2">
            <div class="upload-area" id="upload-area">
                <div class="upload-placeholder">
                    <div class="upload-icon-circle">
                         <i class="fa fa-cloud-upload"></i>
                    </div>
                    <h3>{LANG.upload_your_photo}</h3>
                    <p>{LANG.drag_drop_support}</p>
                    <button class="btn btn-primary btn-upload" onclick="document.getElementById('file-upload').click()">{LANG.click_to_upload}</button>
                    <input type="file" id="file-upload" accept="image/*" style="display:none" onchange="handleFileUpload(this)">
                </div>
            </div>
            <div class="text-center mt-20">
                 <button class="btn btn-outline" onclick="history.back()"><i class="fa fa-arrow-left"></i> {LANG.back}</button>
            </div>
        </div>

        <!-- Step 3: Editor -->
        <div class="step-content" id="step-3" style="display:none;">
            <div class="editor-layout">
                <!-- Canvas Area -->
                <div class="editor-workspace">
                     <div class="canvas-wrapper" id="canvas-wrapper">
                         <canvas id="c"></canvas>
                     </div>
                </div>

                <!-- Tools Area (Bottom Sheet style on Mobile, Sidebar on Desktop) -->
                <div class="editor-tools">
                    <div class="editor-tabs">
                        <div class="main-tab active" onclick="switchMainTab('image')">
                            <i class="fa fa-picture-o"></i> <span>{LANG.tab_image}</span>
                        </div>
                        <div class="main-tab" onclick="switchMainTab('text')">
                            <i class="fa fa-font"></i> <span>{LANG.tab_text}</span>
                        </div>
                    </div>

                    <!-- Image Panel -->
                    <div class="editor-panel active" id="panel-image">
                        <div class="control-group">
                            <div class="control-label">{LANG.zoom}</div>
                            <div class="range-wrapper">
                                <i class="fa fa-search-minus"></i>
                                <input type="range" class="custom-range" id="ctrl-zoom" min="0.1" max="3" step="0.05" value="1" oninput="updateUserImage('scale', parseFloat(this.value))">
                                <i class="fa fa-search-plus"></i>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">{LANG.rotate}</div>
                            <div class="range-wrapper">
                                <i class="fa fa-undo"></i>
                                <input type="range" class="custom-range" id="ctrl-rotate" min="-180" max="180" step="1" value="0" oninput="updateUserImage('angle', parseInt(this.value))">
                                <i class="fa fa-repeat"></i>
                            </div>
                        </div>

                        <div class="action-buttons-row">
                             <button class="btn btn-outline btn-sm" onclick="resetImage()"><i class="fa fa-refresh"></i> Reset</button>
                             <button class="btn btn-outline btn-sm" onclick="document.getElementById('file-upload').click()">{LANG.change_photo}</button>
                        </div>
                    </div>

                    <!-- Text Panel -->
                    <div class="editor-panel" id="panel-text">
                        <button class="btn btn-add-text" onclick="addText()"><i class="fa fa-plus-circle"></i> {LANG.add_text_btn}</button>

                        <div id="no-text-msg">
                            {LANG.select_text_to_edit}
                        </div>

                        <div id="text-edit-area" style="display:none;">
                            <textarea id="ctrl-text-content" class="form-control text-input" rows="1" oninput="updateActiveText('text', this.value)"></textarea>

                            <div class="sub-tabs-scroll">
                                <span class="sub-tab active" onclick="switchSubTab('pos')">{LANG.tab_position}</span>
                                <span class="sub-tab" onclick="switchSubTab('font')">{LANG.tab_font}</span>
                                <span class="sub-tab" onclick="switchSubTab('format')">{LANG.tab_format}</span>
                                <span class="sub-tab" onclick="switchSubTab('color')">{LANG.tab_color}</span>
                                <span class="sub-tab" onclick="switchSubTab('style')">{LANG.tab_outline}</span>
                            </div>

                            <div class="sub-panels-container">
                                <!-- Position -->
                                <div class="sub-panel active" id="sub-pos">
                                    <div class="align-controls">
                                        <button class="btn-icon" onclick="alignText('left')"><i class="fa fa-align-left"></i></button>
                                        <button class="btn-icon" onclick="alignText('center')"><i class="fa fa-align-center"></i></button>
                                        <button class="btn-icon" onclick="alignText('right')"><i class="fa fa-align-right"></i></button>
                                    </div>
                                    <div class="move-controls">
                                        <button class="btn-icon" onclick="moveText('up')"><i class="fa fa-arrow-up"></i></button>
                                        <div class="d-flex">
                                            <button class="btn-icon" onclick="moveText('left')"><i class="fa fa-arrow-left"></i></button>
                                            <button class="btn-icon" onclick="moveText('down')"><i class="fa fa-arrow-down"></i></button>
                                            <button class="btn-icon" onclick="moveText('right')"><i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                    <button class="btn btn-danger btn-block mt-10" onclick="deleteActiveObj()"><i class="fa fa-trash"></i> {LANG.delete}</button>
                                </div>

                                <!-- Font -->
                                <div class="sub-panel" id="sub-font">
                                    <select id="ctrl-fontFamily" class="form-control" onchange="updateActiveText('fontFamily', this.value)">
                                        <option value="Arial" style="font-family:Arial">Arial</option>
                                        <option value="Be Vietnam Pro" style="font-family:'Be Vietnam Pro'">Be Vietnam</option>
                                        <option value="Iwona Display" style="font-family:'Iwona Display'">Iwona</option>
                                        <option value="Viaoda Libre" style="font-family:'Viaoda Libre'">Viaoda Libre</option>
                                        <option value="Bona Nova" style="font-family:'Bona Nova'">Bona Nova</option>
                                        <!-- Santor fallback -->
                                        <option value="Santor" style="font-family:serif">Santor</option>
                                    </select>
                                    <div class="control-group mt-10">
                                        <label>{LANG.size}</label>
                                        <input type="range" class="custom-range" id="ctrl-fontSize" min="10" max="100" oninput="updateActiveText('fontSize', parseInt(this.value))">
                                    </div>
                                </div>

                                <!-- Format -->
                                <div class="sub-panel" id="sub-format">
                                     <div class="style-toggles">
                                        <button class="btn-icon" id="btn-bold" onclick="toggleStyle('fontWeight', 'bold')"><b>B</b></button>
                                        <button class="btn-icon" id="btn-italic" onclick="toggleStyle('fontStyle', 'italic')"><i>I</i></button>
                                        <button class="btn-icon" id="btn-underline" onclick="toggleStyle('underline', true)"><u>U</u></button>
                                    </div>
                                    <div class="control-group">
                                        <label>Line Height</label>
                                        <input type="range" class="custom-range" id="ctrl-lineHeight" min="0.5" max="3" step="0.1" oninput="updateActiveText('lineHeight', parseFloat(this.value))">
                                    </div>
                                     <div class="control-group">
                                        <label>Letter Spacing</label>
                                        <input type="range" class="custom-range" id="ctrl-charSpacing" min="-100" max="500" step="10" oninput="updateActiveText('charSpacing', parseInt(this.value))">
                                    </div>
                                </div>

                                <!-- Color -->
                                <div class="sub-panel" id="sub-color">
                                     <label>{LANG.text_color}</label>
                                     <div class="color-picker-wrapper">
                                        <input type="color" id="ctrl-fill" class="form-control color-input" onchange="updateActiveText('fill', this.value)">
                                     </div>
                                </div>

                                <!-- Style (Outline/Shadow) -->
                                <div class="sub-panel" id="sub-style">
                                    <label>Outline Color</label>
                                    <input type="color" id="ctrl-stroke" class="form-control color-input mb-10" onchange="updateActiveText('stroke', this.value)">
                                    <label>Outline Width</label>
                                    <input type="range" class="custom-range" id="ctrl-strokeWidth" min="0" max="10" oninput="updateActiveText('strokeWidth', parseInt(this.value))">

                                    <hr>
                                    <label>Shadow Color</label>
                                    <input type="color" id="ctrl-shadowColor" class="form-control color-input mb-10" onchange="updateShadow()">
                                    <label>Shadow Blur</label>
                                    <input type="range" class="custom-range" id="ctrl-shadowBlur" min="0" max="50" oninput="updateShadow()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="editor-footer">
                     <button class="btn btn-outline" onclick="setStep(2)"><i class="fa fa-arrow-left"></i> {LANG.back}</button>
                     <button class="btn btn-primary btn-finish" onclick="generatePreview()">{LANG.finish_preview} <i class="fa fa-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <!-- Step 4: Preview -->
        <div class="step-content text-center" id="step-4" style="display:none;">
            <div class="preview-header">
                <h3>{LANG.congratulations}</h3>
                <p>{LANG.success_msg}</p>
            </div>

            <div class="preview-box">
                <img id="final-result-img" class="img-fluid">
            </div>

            <div class="preview-actions">
                <button class="btn btn-secondary" onclick="setStep(3)"><i class="fa fa-pencil"></i> {LANG.continue_editing}</button>
                <!-- BEGIN: allow_use -->
                <button class="btn btn-primary btn-lg btn-download" onclick="downloadResult()"><i class="fa fa-download"></i> {LANG.download_image}</button>
                <!-- END: allow_use -->
            </div>

            <div class="share-info">
                 <p>{LANG.share_with_friends}</p>
                 <!-- Social buttons placeholder -->
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP vars to JS
    var nv_base_siteurl = '{NV_BASE_SITEURL}';
    var nv_lang_data = '{NV_LANG_DATA}';
    var nv_module_name = '{MODULE_NAME}';
    var nv_name_variable = '{NV_NAME_VARIABLE}';
    var nv_op_variable = '{NV_OP_VARIABLE}';
    var nv_lang_variable = '{NV_LANG_VARIABLE}';
    var nv_lang_please_upload_photo = '{LANG.please_upload_photo}';

    // Update App State from PHP
    // We defer the execution to allow avatar.js to load
    window.addEventListener('load', function() {
        if(typeof appState !== 'undefined') {
            appState.frameImg = '{NV_BASE_SITEURL}{ROW.image}'; // Prepend Base URL
            appState.tplId = {ROW.id};
            appState.tplTitle = '{ROW.title}';
            appState.tplViews = {ROW.views};
            appState.tplDownloads = {ROW.downloads};

            // Auto start
            setStep(2);
        }
    });
</script>
<script src="{NV_BASE_SITEURL}themes/default/js/avatar.js"></script>
<!-- END: main -->
