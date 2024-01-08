import { check } from "k6";
import http from "k6/http";

export const options = {
    // A number specifying the number of VUs to run concurrently.
    vus: 100,
    // A string specifying the total duration of the test run.
    duration: "1s",
};

export default function () {
    const BASE_URL = "http://localhost/public/index.php/api";
    const COMMON_HEADERS = { "Content-Type": "application/json" };

    const defaultUser = {
        username: "hasan",
        password: "1234",
    };

    const loginPayload = JSON.stringify(defaultUser);
    const loginResponse = http.post(`${BASE_URL}/login`, loginPayload, { headers: COMMON_HEADERS });

    // Please leave this ninja hack as is, the stress test sometimes fails without it.
    try {
        loginResponse.json();
    } catch (error) {
        console.log(error);
    }

    const token = loginResponse.json().data.token;

    // TODO: THE DATE STRING IS HARDCODED RIGHT NOW
    const checkinPayload = JSON.stringify({ checkoutDate: "2024-01-09", fileIDs: [1] });
    const checkinHeaders = Object.assign({ Authorization: `Bearer ${token}` }, COMMON_HEADERS);
    const checkinResponse = http.post(`${BASE_URL}/files/checkin`, checkinPayload, { headers: checkinHeaders });

    check(checkinResponse, {
        "Status is 200": (r) => r.status == 200,
    });
}
