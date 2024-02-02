let deferredPrompt;
let installButtonDisplayed = false;
const _version = '2';
const _scope = '';

window.addEventListener('beforeinstallprompt', (event) => {
	event.preventDefault();
	deferredPrompt = event;

	if (!installButtonDisplayed) {
		displayInstallButton();
	}
});

function displayInstallButton() {
	const installButton = document.getElementById('install-button');
	if(!installButton) return;

	const userAgent = navigator.userAgent;
	const isIOS = /iPad|Macintosh|iPhone|iPod/.test(userAgent);
	// const isDesktop = !userAgent.match(/(Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone)/i);
	const isAndroidWebView = (userAgent.indexOf('Android') !== -1 && userAgent.indexOf('Mobile') !== -1 && userAgent.indexOf('Build') !== -1);

	if(isIOS && window.navigator.standalone !== true) return;
	if(isAndroidWebView) return;
	// if(!isDesktop) return;

	installButton.style.display = 'inline-block';
	installButton.addEventListener('click', () => {
		installApp();
	});

	installButtonDisplayed = true;
}

function hideInstallButton() {
	const installButton = document.getElementById('install-button');
	if(!installButton) return;
	installButton.style.display = 'none';
}

function installApp() {
	let isIOS = /iPad|Macintosh|iPhone|iPod/.test(navigator.userAgent);
	if (isIOS) {
		$('#modal_profile').modal('hide');
		let modal = $('#modal_install_iOS');
		if(modal.length) modal.modal('show');
		return;
	}

	if(!deferredPrompt) return;
	deferredPrompt.prompt();
	deferredPrompt.userChoice.then((choiceResult) => {
		if (choiceResult.outcome === 'accepted') {
			console.log('User accepted the installation');
		} else {
			console.log('User declined the installation');
		}
		deferredPrompt = null;
		hideInstallButton();
	});
}

function checkInstallPrompt() {
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('/service-worker.js?v='+_version, {
			scope: '/'+_scope
		}).then(function(registration) {
			console.warn('Laravel PWA: Service Worker registered with scope:', registration.scope);

			if (!deferredPrompt && !installButtonDisplayed) {
				displayInstallButton();
			}
		}).catch(function(error) {
			console.error('Laravel PWA: Failed to register Service Worker:', error);
		});
	} else {
		console.warn('Laravel PWA: Service Worker is not supported.');
	}
}

/* Check if Service Worker is registered and display installation button if necessary */
checkInstallPrompt();

/* Display the installation button automatically after a period of time (e.g., 10 seconds) */
setTimeout(function() {
	if (!deferredPrompt && !installButtonDisplayed) {
		displayInstallButton();
	}
}, 5000); /* Time in milliseconds (set to 10 seconds here) */