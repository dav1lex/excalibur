# NanoBid - A smple Auction Platform

This is a auction platform built with PHP. Users can register, login, and bid on items in auctions. Admins can manage users, auctions, and lots. The project is built with a simple structure, not over-engineered.

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
6.  run `composer install` to get phpmailer.
7.  enjoy.

## Default Admin Account

- Email: admin@test.com
- Password: admin123


## Project Structure

```
/
├── config/           # Configuration files
├── controllers/      # Controller classes
├── models/           # Model classes
├── views/            # View templates
│   ├── layouts/      # Layout templates (header, footer)
│   ├── partials/     # Reusable view components
│   ├── home/         # Home page views
│   ├── auth/         # Authentication views
│   ├── admin/        # Admin panel views
│   └── user/         # User panel views
├── public/           # Publicly accessible files
│   ├── css/          # CSS files
│   ├── js/           # JavaScript files
│   └── uploads/      # User uploaded files
├── .htaccess         # Apache configuration
├── index.php         # Application entry point
└── database.sql      # Database schema
```
