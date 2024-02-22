import http from 'k6/http';

export const options = JSON.parse(__ENV.PEST_STRESS_TEST_OPTIONS);

export default () => {

    let userAgent = 'Pest Plugin Stressless (https://pestphp.com) + K6 (https://k6.io)';
    let url = __ENV.PEST_STRESS_TEST_URL;
    let payload = options.payload ? options.payload : {};
    let method = options.method ? options.method : 'get';

    switch (method) {
        case 'delete':
            http.del(url, null, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'get':
            http.get(url, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'head':
            http.head(url, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'options':
            http.options(url, Object.keys(payload).length ? payload : null, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'patch':
            http.patch(url, Object.keys(payload).length ? payload : null, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'put':
            http.put(url, Object.keys(payload).length ? payload : null, {
                headers: { 'user-agent': userAgent },
            });
            break;
        case 'post':
            http.post(url, payload, {
                headers: { 'user-agent': userAgent },
            });
            break;
    }
}

export function handleSummary(data) {
    return {
        [__ENV.PEST_STRESS_TEST_SUMMARY_PATH]: JSON.stringify(data),
    };
}
