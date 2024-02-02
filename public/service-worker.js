const _version = "2";

self.addEventListener("install", function(event) {
	console.log('WORKER: install event in progress.');

	const cacheName = 'fundamentals';
	const filesToCache = [];

	event.waitUntil(
		caches.open(cacheName)
		.then(function(cache) {
			return cache.addAll(filesToCache);
		})
		.then(function() {
			console.log('WORKER: install completed');
		})
	);
});

self.addEventListener("activate", function(event) {
  console.log('WORKER: activate event in progress.');

  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName.startsWith(_version)) {
            return; // Mantém o cache atual, não exclui
          }
          
          return caches.delete(cacheName); // Exclui caches desatualizados
        })
      );
    })
    .then(function() {
      console.log('WORKER: activate completed.');
    })
  );
});

self.addEventListener("fetch", function(event) {
	if (event.request.method !== 'GET') {
		return;
	}

	const excludedUrls = [
	'/livewire',
	'/manifest.json',
	];

	let session = "others";

	if (excludedUrls.some(url => event.request.url.includes(url))) {
		event.respondWith(fetch(event.request, { cache: 'no-store' }));
		return;
	}

	// if (event.request.url.includes('mercadolivre.com') || event.request.url.includes('mercadolibre.com')) {
	// 	event.respondWith(fetch(event.request, { cache: 'no-store' }));
	// 	return;
	// }

	if (event.request.destination === 'image' || event.request.destination === 'style') {
		session = event.request.destination;

		// if (event.request.url.indexOf("/produtos") !== -1 || event.request.url.indexOf("/empresa-background") !== -1) {
		// 	event.respondWith(fetch(event.request, { cache: 'no-store' }));
		// 	return;
		// }
	} else if (event.request.destination === 'document') {
		return;
	}

	event.respondWith(
		caches.open(_version + '_' + session).then(function(cache) {
			return cache.match(event.request).then(function(cachedResponse) {
				var fetchPromise = fetch(event.request).then(function(networkResponse) {
					if (networkResponse.status !== 206) {
						cache.put(event.request, networkResponse.clone());
					}
					return networkResponse;
				}).catch(function() {
					return cachedResponse; /* Retorna a resposta em cache se a solicitação de rede falhar */
				});

				return cachedResponse || fetchPromise;
			});
		})
	);
});

/*
Antigo

	self.addEventListener("activate", function(event) {
		console.log('WORKER: activate event in progress.');

		event.waitUntil(
			caches
			.keys()
			.then(function (keys) {
				return Promise.all(
					keys
					.filter(function (key) {
						return !key.startsWith(_version);
					})
					.map(function (key) {
						return caches.delete(key);
					})
				);
			})
			.then(function() {
				console.log('WORKER: activate completed.');
			})
		);
	});

	self.addEventListener("fetch", function(event) {
	  if (event.request.method !== 'GET') {
	    return;
	  }

	  if(event.request.url.includes('/api/order/register') || event.request.url.includes('/api/usuario/pedidos') || event.request.url.includes('/api/order/update') ||
	    event.request.url.includes('/api/table/register') || event.request.url.includes('/api/usuario/mesas') || event.request.url.includes('/api/table/update') || event.request.url.includes('/api/mesas') ||
	    event.request.url.includes('/api/produto') || event.request.url.includes('/api/taxa_entrega') || event.request.url.includes('/api/usuarios/enderecos/change_tipo') || event.request.url.includes('/cupom') ||
	    event.request.url.includes('/api/usuario/promo') || event.request.url.includes('/api/usuario/promo/redeem') || event.request.url.includes('/manifest.json')) {
	    return;
	  }

	  let session = "others";
	  if(event.request.destination == 'image' || event.request.destination == 'style') {
	    session = event.request.destination;

	    if(event.request.url.indexOf("/produtos")) return;
	    if(event.request.url.indexOf("/empresa-background") != -1) return;
	  }else if(event.request.destination == 'document'){
	    return;
	  }

	  event.respondWith(
	    caches
	      .match(event.request)
	      .then(function(cached) {
	        var networked = fetch(event.request)
	          .then(fetchedFromNetwork, unableToResolve)
	          .catch(unableToResolve);

	        return cached || networked;

	        function fetchedFromNetwork(response) {
	          var cacheCopy = response.clone();

	          caches
	            .open(_version + '_' + session)
	            .then(function add(cache) {
	              cache.put(event.request, cacheCopy);
	            });

	          return response;
	        }

	        function unableToResolve () {
	          console.log('WORKER: fetch request failed in both cache and network.');

	          return new Response('<h1>Service Unavailable</h1>', {
	            status: 503,
	            statusText: 'Service Unavailable',
	            headers: new Headers({
	              'Content-Type': 'text/html'
	            })
	          });
	        }
	      })
	  );
	});
*/

/*
	self.addEventListener('push', function(event) {
	console.log('[Service Worker] Push Received.');
	console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);

	const title = 'Push Codelab';
	const options = {
	body: 'Yay it works.',
	icon: 'images/icon.png',
	badge: 'images/badge.png'
	};

	const notificationPromise = self.registration.showNotification(title, options);
	event.waitUntil(notificationPromise);
	});

	self.addEventListener('notificationclick', function(event) {
	console.log('[Service Worker] Notification click Received.');

	event.notification.close();

	event.waitUntil(
	clients.openWindow('https://developers.google.com/web/')
	);
	});
*/