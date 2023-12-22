# Source Safe Clone

## Description

## Setup

You have to have php installed on your machine.

Run the following steps:

- Copy `.env.example` to `.env`
- Create a `MYSQL` database file called `sourcesafe`
- Run `php artisan migrate:fresh --seed` to initialize the database.
- Run `php artisan serve` to listen on the default port (8000).
