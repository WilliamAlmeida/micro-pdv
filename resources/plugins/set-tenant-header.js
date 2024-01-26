export default function(tenant, headerKey = 'X-Tenant') {
    handleLivewireCalls(tenant, headerKey)
    handleFetchCalls(tenant, headerKey)
    handleXMLHttpCalls(tenant, headerKey)
    handleAxiosCalls(tenant, headerKey)
}

function handleLivewireCalls(tenant, headerKey) {
    if (! window.Livewire) return;

    Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
        options.headers['X-Tenant'] = tenant;
    });

    // window.Livewire.connection.headers = {
    //     ...window.Livewire.connection.headers,
    //     [headerKey]: tenant
    // };
}

function handleFetchCalls(tenant, headerKey) {
    const { fetch: originalFetch } = window;
    window.fetch = async (...args) => {
        let [resource, config = {} ] = args;

        config.headers = {
            ...config.headers,
            [headerKey]: tenant
        }

        return await originalFetch(resource, config);
    };
}

function handleXMLHttpCalls(tenant, headerKey) {
    XMLHttpRequest.prototype.orignalOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open   = function () {
        this.orignalOpen.apply(this, arguments);
        this.setRequestHeader(headerKey, tenant);
    };
}

function handleAxiosCalls(tenant, headerKey) {
    if (! window.axios) return;

    window.axios.defaults.headers.common[headerKey] = tenant;
}