# Source Safe Clone

## Description

## Setup

You have to have php installed on your machine.

Run the following steps:

- Copy `.env.example` to `.env`
- Create an `SQLite` database file using `touch database/db.sqlite`.
- Specify the database path which you have just created using the `DB_DATABASE` key in `.env`. (Path must be absolute)
- Run `php artisan migrate:fresh --seed`.
- Run `php artisan serve` to listen on the default port.
