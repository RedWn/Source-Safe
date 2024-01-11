import { check } from "k6";
import http from "k6/http";

export const options = {
    vus: 100,
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
    const token = loginResponse.json().data.token;

    // Take a look at "CheckController" to learn about valid checkout date format.
    // Today < checkoutDate <= 3 days
    const checkoutDate = "2024-01-12";

    const checkinPayload = JSON.stringify({ checkoutDate, fileIDs: [1] });
    const checkinHeaders = Object.assign({ Authorization: `Bearer ${token}` }, COMMON_HEADERS);
    const checkinResponse = http.post(`${BASE_URL}/files/checkin`, checkinPayload, { headers: checkinHeaders });

    check(checkinResponse, {
        "200 Status": (r) => r.status == 200,
    });
}
