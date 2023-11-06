import http from 'k6/http';

export const options = JSON.parse(__ENV.PEST_STRESS_TEST_OPTIONS);

export default () => {
    http.get(__ENV.PEST_STRESS_TEST_URL, {
        headers: { 'user-agent': 'Pest Plugin Stressless (https://pestphp.com) + K6 (https://k6.io)' },
    });
}

export function handleSummary(data) {
    return {
        [__ENV.PEST_STRESS_TEST_SUMMARY_PATH]: JSON.stringify(data),
    };
}
