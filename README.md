# Source Safe Clone

A 5th-year university project for the Network Applications course. It's a minimal clone of the concepts found in [Microsoft's Visual SourceSafe](https://en.wikipedia.org/wiki/Microsoft_Visual_SourceSafe)

## Introduction

The application is divided into two parts: the front-end and the back-end. The back-end, which is what you're currently viewing, is written in PHP using Laravel. The front-end can be found [here](https://github.com/IyadAlanssary/Source-Safe).

## Features

-   Upload/Download files from the projects that you're in.
-   Check in a file so that only you can edit/delete it.
-   Create new projects and assign users to them (admins only can do this)
-   Create and delete folders

## Setup

You have to have php installed on your machine (use Laragon or XAMPP if on windows).

Run the following steps:

-   Copy `.env.example` to `.env`
-   Create a `MySQL` database file called `sourcesafe`
-   Run `php artisan migrate:fresh --seed` to initialize the database.
-   Run `php artisan serve` to listen on the default port (8000).

## Testing

Please make sure you have graphana's `k6` installed on your machine. (https://grafana.com/docs/k6/latest/get-started/installation/)

There's a race condition in the app concerning file check-in. When multiple users are trying to check-in a file concurrently, only one user should be able to claim the file. Others requests should fail.

I highly recommend reading the test file `resources/js/checkin-test.js` before running it.

To run the test:

-   `cd resources`
-   `npm install`
-   `k6 run js/checkin-test.js`
