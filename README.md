# NanoBid - A smple Auction Platform

This is a auction platform built with PHP.The project is built with a simple structure, not overengineered.

## Featurs:
*   User registration and login
*   Email confirmation
*   Password reset
*   Admin dashboard for managin users and auctions
*   Users can place bidsor proxy bids on lots
*   Users have a watchlist
*   auctions have different statuses (draft, upcoming, live, ended)

## Set up locally
1.  Xampp.
2.  create a mysql database and import `database.sql`.
3.  copy files to htdocs folder.
4.  configure database connection in `config/config.php`.
5.  you need to configure `utils/EmailService.php` with your smtp details.
6.  get composer, then run `composer install` to get phpmailer.
7.  enjoy.

## Default Admin Account

- Email: admin@test.com
- Password: admin123

## live link
[https://titancode.pl/test](https://titancode.pl/test)

