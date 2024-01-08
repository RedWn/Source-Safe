# Source Safe Clone

## Description

## Setup

You have to have php installed on your machine.

Run the following steps:

-   Copy `.env.example` to `.env` (Note that `.env.example` doesn't contain the Telegram API credentials. To get them, contact one of the project's maintainers)
-   Create a `MySQL` database file called `sourcesafe`
-   Run `php artisan migrate:fresh --seed` to initialize the database.
-   Run `php artisan serve` to listen on the default port (8000).

## Testing

Please make sure you have graphana's `k6` installed on your machine. (https://grafana.com/docs/k6/latest/get-started/installation/)

There's a race condition in the app concerning file checkin. When multiple users are trying to checkin a file concurrently, only one user should be able to check it in.

I highly recommend reading the test file `resources/js/checkin-test.js` before running it.

To run the test:

-   `cd resources`
-   `npm install`
-   `k6 run js/checkin-test.js`

## Logging

This project logs info to a public (test) Telegram channel just as a proof of concept.

Tutorial: https://dev.to/dotmarn/how-to-send-application-logs-to-telegram-in-laravel-1l12

Make sure not to spam the bot with too much logging, or else it will block you.
