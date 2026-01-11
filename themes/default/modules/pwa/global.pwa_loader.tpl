<!-- BEGIN: main -->
<link rel="manifest" href="{MANIFEST_URL}">

<div id="pwa-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
    <div id="pwa-install-btn" style="display: none;">
        <button onclick="pwaInstallApp()" class="btn btn-success btn-sm" style="border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
            <i class="fa fa-download" aria-hidden="true" style="font-size: 20px;"></i>
        </button>
    </div>
    <div id="pwa-subscribe-btn" style="display: none;">
        <button onclick="pwaSubscribeUser()" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
            <i class="fa fa-bell" aria-hidden="true" style="font-size: 20px;"></i>
        </button>
    </div>
</div>

<script>
let deferredPrompt;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('{SW_URL}')
        .then(function(registration) {
            console.log('PWA ServiceWorker registration successful with scope: ', registration.scope);

            // Check subscription state
            if ('{VAPID_PUBLIC_KEY}' !== '') {
                registration.pushManager.getSubscription().then(function(sub) {
                    if (sub === null) {
                        // Not subscribed, show button
                        document.getElementById('pwa-subscribe-btn').style.display = 'block';
                    } else {
                        // Already subscribed
                         console.log('User is subscribed');
                    }
                });
            }
        }, function(err) {
            console.log('PWA ServiceWorker registration failed: ', err);
        });
    });
}

// Handle Install Prompt
window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  // Stash the event so it can be triggered later.
  deferredPrompt = e;
  // Update UI notify the user they can install the PWA
  document.getElementById('pwa-install-btn').style.display = 'block';
});

function pwaInstallApp() {
    // Hide the app provided install promotion
    document.getElementById('pwa-install-btn').style.display = 'none';
    // Show the install prompt
    if (deferredPrompt) {
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
            } else {
                console.log('User dismissed the install prompt');
            }
            deferredPrompt = null;
        });
    }
}

function pwaSubscribeUser() {
    if ('serviceWorker' in navigator && '{VAPID_PUBLIC_KEY}' !== '') {
        navigator.serviceWorker.ready.then(function(registration) {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array('{VAPID_PUBLIC_KEY}')
            };
            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then(function(pushSubscription) {
            console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            // Send to server
            fetch('{NV_BASE_SITEURL}index.php?nv={MODULE_NAME}&op=subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'endpoint=' + encodeURIComponent(pushSubscription.endpoint) +
                      '&keys[p256dh]=' + encodeURIComponent(pushSubscription.toJSON().keys.p256dh) +
                      '&keys[auth]=' + encodeURIComponent(pushSubscription.toJSON().keys.auth)
            }).then(res => res.json())
              .then(data => {
                  console.log('Server response', data);
                  if (data.status === 'ok') {
                      document.getElementById('pwa-subscribe-btn').style.display = 'none';
                      alert('Subscribed successfully!');
                  }
              });
        });
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
</script>
<!-- END: main -->
