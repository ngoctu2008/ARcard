<!-- BEGIN: main -->
<style>
/* Custom Style for Professional Tool */
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
    color: #666;
    margin-right: 10px;
    width: 20px;
    text-align: center;
}
/* Checkbox styling */
.checkbox label, .radio label {
    padding-left: 5px;
}

/* --- Scrollable Tabs CSS --- */
.aiprompts-detail .tabs-wrapper {
    position: relative;
    background: #fff;
    border-radius: 10px;
    padding: 0 10px; /* Padding for icons */
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    width: 100%;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.aiprompts-detail .tabs-icon {
    position: absolute;
    top: 0;
    height: 100%;
    width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    background: #fff;
    z-index: 2;
    color: #555;
    transition: 0.3s ease;
}

.aiprompts-detail .tabs-icon:hover {
    background: #efefef;
    color: #009688;
}

.aiprompts-detail .icon-left {
    left: 0;
    border-radius: 10px 0 0 10px;
    background: linear-gradient(90deg, #fff 70%, transparent);
    display: none; /* Initially hidden */
}

.aiprompts-detail .icon-right {
    right: 0;
    border-radius: 0 10px 10px 0;
    background: linear-gradient(270deg, #fff 70%, transparent);
}

.aiprompts-detail .tabs-box {
    display: flex;
    gap: 10px;
    list-style: none;
    overflow-x: auto; /* Allow horizontal scroll */
    scroll-behavior: smooth;
    padding: 15px 40px; /* Space for icons */
    margin: 0;
    width: 100%;
    scrollbar-width: none; /* Firefox */
}

.aiprompts-detail .tabs-box::-webkit-scrollbar {
    display: none; /* Chrome, Safari */
}

.aiprompts-detail .tab-item {
    cursor: pointer;
    font-size: 1rem;
    white-space: nowrap; /* No wrapping */
    background: #f2f2f2;
    padding: 10px 20px;
    border-radius: 30px;
    border: 1px solid transparent;
    color: #555;
    transition: all 0.3s ease;
    user-select: none;
}

.aiprompts-detail .tab-item a {
    color: inherit;
    text-decoration: none;
    display: block;
}

.aiprompts-detail .tab-item:hover {
    background: #e0e0e0;
    color: #333;
}

.aiprompts-detail .tab-item.active {
    color: #fff;
    background: #009688;
    border-color: #009688;
}
</style>

<div class="aiprompts-detail">
    <div class="row">
        <div class="col-md-24">
            <!-- Scrollable Tabs Navigation -->
            <div class="tabs-wrapper">
                <div class="tabs-icon icon-left"><i class="fa fa-angle-left"></i></div>
                <ul class="tabs-box">
                    <!-- BEGIN: tab -->
                    <li class="tab-item {TAB.active}"><a href="{TAB.link}">{TAB.title}</a></li>
                    <!-- END: tab -->
                </ul>
                <div class="tabs-icon icon-right"><i class="fa fa-angle-right"></i></div>
            </div>
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
$(document).ready(function(){
    // Collapse icon toggling
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find(".fa-angle-right").removeClass("fa-angle-right").addClass("fa-angle-down");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-right");
    });

    // --- Scrollable Tabs Logic ---
    const tabsBox = document.querySelector(".tabs-box");
    if(tabsBox) {
        const arrowIcons = document.querySelectorAll(".tabs-icon");

        const handleIcons = () => {
            let scrollVal = Math.round(tabsBox.scrollLeft);
            let maxScrollableWidth = tabsBox.scrollWidth - tabsBox.clientWidth;

            let iconLeft = document.querySelector(".icon-left");
            let iconRight = document.querySelector(".icon-right");

            if(iconLeft) iconLeft.style.display = scrollVal > 0 ? "flex" : "none";
            // Tiny buffer for float calc
            if(iconRight) iconRight.style.display = maxScrollableWidth - scrollVal > 1 ? "flex" : "none";
        }

        arrowIcons.forEach(icon => {
            icon.addEventListener("click", () => {
                let scrollWidth = icon.classList.contains("icon-left") ? -350 : 350;
                tabsBox.scrollLeft += scrollWidth;
                setTimeout(() => handleIcons(), 50);
            });
        });

        tabsBox.addEventListener("scroll", handleIcons);

        // Drag scrolling
        let isDragging = false;
        tabsBox.addEventListener("mousedown", () => isDragging = true);
        tabsBox.addEventListener("mouseup", () => isDragging = false);
        tabsBox.addEventListener("mouseleave", () => isDragging = false);
        tabsBox.addEventListener("mousemove", (e) => {
            if(!isDragging) return;
            tabsBox.classList.add("dragging");
            tabsBox.scrollLeft -= e.movementX;
            handleIcons();
        });

        // Auto scroll to active tab
        const activeTab = document.querySelector(".tab-item.active");
        if(activeTab) {
            // Use setTimeout to ensure rendering is done
            setTimeout(() => {
                activeTab.scrollIntoView({
                    behavior: 'auto',
                    inline: 'center',
                    block: 'nearest'
                });
                handleIcons();
            }, 100);
        } else {
            handleIcons();
        }
    }
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
        if (Array.isArray(value)) {
            value = value.join(', ');
        }
        var cleanKey = key.replace('[]', '');
        var regex = new RegExp('\\{' + cleanKey + '\\}', 'g');
        prompt = prompt.replace(regex, value);
    }

    $('#result-area').val(prompt);
    $('#result-container').show();
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
