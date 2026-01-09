# Liberty Tickets

Internal ticketing system for bug reports and feature requests (role-based).

## Install

```bash
composer require julia/tickets
php artisan vendor:publish --tag=tickets-config
php artisan vendor:publish --tag=tickets-views
php artisan migrate
