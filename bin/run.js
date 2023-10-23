import http from 'k6/http';

export const options = JSON.parse(__ENV.PEST_STRESS_TEST_OPTIONS);

export default () => {
    const result = http.get(__ENV.PEST_STRESS_TEST_URL);
}

export function handleSummary(data) {
    return {
        'summary.json': JSON.stringify(data),
    };
}
