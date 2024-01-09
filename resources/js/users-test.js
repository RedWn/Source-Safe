// @ts-check
import { check } from "k6";
import http from "k6/http";

export const options = {
    vus: 100,
    duration: "1s",
};

export default function () {
    const BASE_URL = "http://localhost/public/index.php/api";
    const COMMON_HEADERS = { "Content-Type": "application/json" };

    const usersResponse = http.get(`${BASE_URL}/users/search/hasan`, { headers: COMMON_HEADERS });
    check(usersResponse, {
        "Status is 200": (r) => r.status == 200,
    });
}
