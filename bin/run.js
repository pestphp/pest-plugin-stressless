import http from 'k6/http';

export const options = JSON.parse(__ENV.PEST_STRESS_TEST_OPTIONS);

export default () => {

    let userAgent = 'Pest Plugin Stressless (https://pestphp.com) + K6 (https://k6.io)';
    let url = __ENV.PEST_STRESS_TEST_URL;
    let payload = options.payload ? JSON.stringify(options.payload) : JSON.stringify({});
    let method = options.method ? options.method : 'get';

    switch (method) {
        case 'get':
            http.get(url, {
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
