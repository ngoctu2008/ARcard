// Global App State
var appState = {
    step: 1,
    canvas: null,
    frameImg: '', // Will be set from TPL
    userImg: null,
    frameObj: null,
    canvasWidth: 800,
    canvasHeight: 800,
    tplId: 0,
    tplTitle: '',
    tplViews: 0,
    tplDownloads: 0
};

// Initialize Fabric Canvas
function initCanvas() {
    if(appState.canvas) {
         // If canvas exists, just ensure it's sized correctly in case it was hidden
         appState.canvas.calcOffset();
         appState.canvas.requestRenderAll();
         return;
    }

    var wrapper = document.getElementById('canvas-wrapper');
    if (!wrapper) return;

    // Use a fixed logical size, CSS handles the display size via scaling if needed
    // But for Fabric, the width/height property controls the drawing buffer.
    // If the wrapper is hidden, clientWidth might be 0.
    // We should force dimensions if we know them (appState.canvasWidth).

    appState.canvas = new fabric.Canvas('c', {
        width: appState.canvasWidth,
        height: appState.canvasHeight,
        preserveObjectStacking: true,
        selection: true,
        backgroundColor: '#fff' // Set white background to see canvas clearly
    });

    // Event Listeners
    appState.canvas.on('selection:created', onObjSelect);
    appState.canvas.on('selection:updated', onObjSelect);
    appState.canvas.on('selection:cleared', onObjClear);

    loadFrame();
}

function loadFrame() {
    // Load Frame Overlay
    if(appState.frameImg && appState.canvas) {
        // Check if frame is already there
        if(appState.frameObj) {
            appState.canvas.remove(appState.frameObj);
        }

        fabric.Image.fromURL(appState.frameImg, function(img) {
            if(!img) {
                console.error('Failed to load frame image: ' + appState.frameImg);
                return;
            }
            // Scale frame to fit canvas
            img.scaleToWidth(appState.canvasWidth);
            img.scaleToHeight(appState.canvasHeight);
            img.selectable = false;
            img.evented = false;

            appState.frameObj = img;
            appState.canvas.add(img);
            img.bringToFront(); // Ensure frame is on top
            appState.canvas.requestRenderAll();
        }, { crossOrigin: 'anonymous' });
    }
}

// Navigation Logic
function setStep(step) {
    // Validation for step 3 (must have image)
    if(step === 3 && !appState.userImg) {
        alert(typeof nv_lang_please_upload_photo !== 'undefined' ? nv_lang_please_upload_photo : 'Please upload a photo first');
        return;
    }

    appState.step = step;

    // Update nav UI
    document.querySelectorAll('.step-nav li').forEach(function(li) {
        li.classList.remove('active');
    });
    var currentNav = document.getElementById('step-nav-' + step);
    if(currentNav) currentNav.classList.add('active');

    // Mark previous steps completed
    for(var i=1; i<step; i++) {
         var prev = document.getElementById('step-nav-' + i);
         if(prev) prev.classList.add('completed');
    }

    // Show Content
    document.querySelectorAll('.step-content').forEach(function(d) {
        d.style.display = 'none';
    });

    var content = document.getElementById('step-' + step);
    if(content) {
        content.style.display = 'block';
    }

    // If entering Editor step, ensure canvas is ready
    if(step === 3) {
        // Use timeout to allow DOM to render block display so offsets are calculated
        setTimeout(function() {
             initCanvas();
             // Force refresh logic
             if(appState.canvas) {
                 appState.canvas.calcOffset();
                 appState.canvas.requestRenderAll();
             }
        }, 200);
    }
}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var imgObj = new Image();
            imgObj.src = e.target.result;
            imgObj.onload = function() {
                // Ensure we switch to step 3 first so canvas is created/visible
                setStep(3);
                // Then load image (give a small delay for initCanvas inside setStep to fire if needed)
                setTimeout(function(){
                    loadImageToCanvas(imgObj);
                }, 300);
            };
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function loadImageToCanvas(imgElem) {
    if(!appState.canvas) initCanvas();
    if(appState.userImg) appState.canvas.remove(appState.userImg);

    var imgInstance = new fabric.Image(imgElem);
    appState.userImg = imgInstance;

    // Scale image to cover the canvas (like object-fit: cover)
    var scale = Math.max(appState.canvasWidth / imgInstance.width, appState.canvasHeight / imgInstance.height);

    imgInstance.set({
        left: appState.canvasWidth/2, top: appState.canvasHeight/2,
        originX: 'center', originY: 'center',
        scaleX: scale, scaleY: scale
    });

    appState.canvas.add(imgInstance);
    imgInstance.sendToBack(); // User image goes to back

    // IMPORTANT: Bring frame to front again in case it got covered
    if(appState.frameObj) {
        appState.frameObj.bringToFront();
    } else {
        loadFrame(); // Reload if missing
    }

    appState.canvas.setActiveObject(imgInstance);
    appState.canvas.requestRenderAll();

    // Reset controls
    var inputZoom = document.getElementById('ctrl-zoom');
    if(inputZoom) inputZoom.value = scale;

    var inputRot = document.getElementById('ctrl-rotate');
    if(inputRot) inputRot.value = 0;

    switchMainTab('image');
}

function switchMainTab(tab) {
    document.querySelectorAll('.main-tab').forEach(function(e) { e.classList.remove('active'); });
    document.querySelectorAll('.editor-panel').forEach(function(e) { e.classList.remove('active'); });

    if(tab === 'image') {
        var tabEl = document.querySelector('.main-tab[onclick*="image"]');
        if(tabEl) tabEl.classList.add('active');

        var panel = document.getElementById('panel-image');
        if(panel) panel.classList.add('active');

        if(appState.userImg && appState.canvas) {
            appState.canvas.setActiveObject(appState.userImg);
            appState.canvas.requestRenderAll();
        }
    } else {
        var tabEl = document.querySelector('.main-tab[onclick*="text"]');
        if(tabEl) tabEl.classList.add('active');

        var panel = document.getElementById('panel-text');
        if(panel) panel.classList.add('active');

        if(appState.canvas) {
            var texts = appState.canvas.getObjects('i-text');
            if(texts.length > 0) {
                appState.canvas.setActiveObject(texts[0]);
                appState.canvas.requestRenderAll();
            }
        }
    }
}

function switchSubTab(tab) {
    document.querySelectorAll('.sub-tab').forEach(function(e) { e.classList.remove('active'); });
    document.querySelectorAll('.sub-panel').forEach(function(e) { e.classList.remove('active'); });

    // Activate tab
    var subTabEl = document.querySelector('.sub-tab[onclick*="' + tab + '"]');
    if(subTabEl) subTabEl.classList.add('active');

    var subPanel = document.getElementById('sub-' + tab);
    if(subPanel) subPanel.classList.add('active');
}

// Image Ops
function updateUserImage(prop, val) {
    if(appState.userImg) {
        if (prop === 'scale') {
            appState.userImg.scale(val);
        } else {
            appState.userImg.set(prop, val);
        }
        appState.canvas.requestRenderAll();
    }
}
function resetImage() {
     if(appState.userImg) {
         appState.userImg.set({ angle: 0 });
         document.getElementById('ctrl-rotate').value = 0;
         appState.canvas.requestRenderAll();
     }
}

// Text Ops
function addText() {
    if(!appState.canvas) return;
    var text = new fabric.IText('New Text', {
        left: appState.canvasWidth/2, top: appState.canvasHeight/2,
        fontFamily: 'Arial', fontSize: 40, fill: '#333333',
        originX: 'center', originY: 'center'
    });
    appState.canvas.add(text);

    // Ensure text is on top of user image but maybe below/above frame?
    // Usually text is ON TOP of frame for avatars? Or below?
    // User requirement: "User upload photo... goes BEHIND frame".
    // Text usually goes ON TOP of everything.
    text.bringToFront();

    appState.canvas.setActiveObject(text);
    switchMainTab('text');
}

function onObjSelect(e) {
    var obj = e.selected ? e.selected[0] : appState.canvas.getActiveObject();
    if(obj && (obj.type === 'i-text' || obj.type === 'text')) {
        var noTextMsg = document.getElementById('no-text-msg');
        if(noTextMsg) noTextMsg.style.display = 'none';

        var textEditArea = document.getElementById('text-edit-area');
        if(textEditArea) textEditArea.style.display = 'block';

        // Sync Inputs
        var el;
        if(el = document.getElementById('ctrl-text-content')) el.value = obj.text;
        if(el = document.getElementById('ctrl-fontSize')) el.value = obj.fontSize;
        if(el = document.getElementById('ctrl-fill')) el.value = obj.fill;
        if(el = document.getElementById('ctrl-charSpacing')) el.value = obj.charSpacing;
        if(el = document.getElementById('ctrl-lineHeight')) el.value = obj.lineHeight;
        if(el = document.getElementById('ctrl-stroke')) el.value = obj.stroke || '#000000';
        if(el = document.getElementById('ctrl-strokeWidth')) el.value = obj.strokeWidth || 0;
        if(el = document.getElementById('ctrl-fontFamily')) el.value = obj.fontFamily;

        if(obj.shadow) {
            if(el = document.getElementById('ctrl-shadowColor')) el.value = obj.shadow.color;
            if(el = document.getElementById('ctrl-shadowBlur')) el.value = obj.shadow.blur;
            if(el = document.getElementById('ctrl-shadowX')) el.value = obj.shadow.offsetX;
            if(el = document.getElementById('ctrl-shadowY')) el.value = obj.shadow.offsetY;
        }

        // Sync Styles Buttons
        var btn;
        if(btn = document.getElementById('btn-bold')) btn.classList.toggle('active', obj.fontWeight === 'bold');
        if(btn = document.getElementById('btn-italic')) btn.classList.toggle('active', obj.fontStyle === 'italic');
        if(btn = document.getElementById('btn-underline')) btn.classList.toggle('active', !!obj.underline);

        // Ensure Text tab is active visually
        var textPanel = document.getElementById('panel-text');
        if(textPanel && !textPanel.classList.contains('active')) {
            switchMainTab('text');
        }
    } else if (obj === appState.userImg) {
         var el;
         if(el = document.getElementById('ctrl-zoom')) el.value = obj.scaleX;
         if(el = document.getElementById('ctrl-rotate')) el.value = obj.angle;
    }
}

function onObjClear() {
     var noTextMsg = document.getElementById('no-text-msg');
     if(noTextMsg) noTextMsg.style.display = 'block';

     var textEditArea = document.getElementById('text-edit-area');
     if(textEditArea) textEditArea.style.display = 'none';
}

function updateActiveText(prop, val) {
    if(!appState.canvas) return;
    var obj = appState.canvas.getActiveObject();
    if(obj && (obj.type === 'i-text' || obj.type === 'text')) {
        if(prop === 'text') obj.set('text', val);
        else obj.set(prop, val);
        appState.canvas.requestRenderAll();
    }
}

function toggleStyle(prop, val) {
     if(!appState.canvas) return;
     var obj = appState.canvas.getActiveObject();
     if(obj) {
         if(prop === 'underline') {
             obj.set('underline', !obj.underline);
             var btn = document.getElementById('btn-underline');
             if(btn) btn.classList.toggle('active');
         } else {
             var current = obj.get(prop);
             var newVal = current === val ? 'normal' : val;
             obj.set(prop, newVal);
             // Update btn state
             if(prop === 'fontWeight') {
                 var btn = document.getElementById('btn-bold');
                 if(btn) btn.classList.toggle('active', newVal === val);
             }
             if(prop === 'fontStyle') {
                 var btn = document.getElementById('btn-italic');
                 if(btn) btn.classList.toggle('active', newVal === val);
             }
         }
         appState.canvas.requestRenderAll();
     }
}

function alignText(align) {
     updateActiveText('textAlign', align);
}

function moveText(dir) {
    if(!appState.canvas) return;
    var obj = appState.canvas.getActiveObject();
    if(obj) {
        var step = 5;
        if(dir === 'up') obj.top -= step;
        if(dir === 'down') obj.top += step;
        if(dir === 'left') obj.left -= step;
        if(dir === 'right') obj.left += step;
        obj.setCoords();
        appState.canvas.requestRenderAll();
    }
}

function updateShadow() {
    if(!appState.canvas) return;
    var obj = appState.canvas.getActiveObject();
    if(obj) {
        var color = document.getElementById('ctrl-shadowColor').value;
        var blur = parseInt(document.getElementById('ctrl-shadowBlur').value);
        var x = parseInt(document.getElementById('ctrl-shadowX').value);
        var y = parseInt(document.getElementById('ctrl-shadowY').value);

        obj.setShadow({ color: color, blur: blur, offsetX: x, offsetY: y });
        appState.canvas.requestRenderAll();
    }
}

function deleteActiveObj() {
    if(!appState.canvas) return;
    var obj = appState.canvas.getActiveObject();
    if(obj) {
        appState.canvas.remove(obj);
        appState.canvas.discardActiveObject();
        appState.canvas.requestRenderAll();
    }
}

// --- Finalize ---
function generatePreview() {
    if(!appState.canvas) return;
    appState.canvas.discardActiveObject();
    appState.canvas.requestRenderAll();

    // Generate high quality PNG
    var dataURL = appState.canvas.toDataURL({ format: 'png', multiplier: 2 });

    var img = document.getElementById('final-result-img');
    if(img) img.src = dataURL;

    var title = document.getElementById('preview-title');
    if(title) title.innerText = appState.tplTitle;

    setStep(4);
}

function downloadResult() {
    var img = document.getElementById('final-result-img');
    if(img && img.src) {
        var link = document.createElement('a');
        link.download = 'avatar_' + Date.now() + '.png';
        link.href = img.src;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        var step5 = document.getElementById('step-nav-5');
        if(step5) step5.classList.add('completed');

        trackAction('download', appState.tplId);
    }
}

function trackAction(action, id) {
    // Basic wrapper to call module ajax
    if(typeof $ !== 'undefined' && id > 0) {
        // NV Variables must be defined in global scope or TPL
        // Here we try to use standard params
        var url = nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_op_variable + '=ajax';

        $.post(url, {
            action: action,
            id: id,
            tokend: nv_check_session // Ensure token if needed
        });
    }
}
