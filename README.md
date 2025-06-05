# NanoBid

A simple, clean auction platform for auction houses built with PHP, MySQL, and Bootstrap 5.

## Setup Instructions

1. **Database Setup**
   - Create a new database named `auction_platform` in phpMyAdmin
   - Import the `database.sql` file to create tables and initial admin user

2. **Configuration**
   - Update the `config/config.php` file with your database credentials if needed
   - Adjust the `BASE_URL` constant to match your local environment

3. **Web Server**
   - Make sure you have PHP 8.0+ and MySQL running (XAMPP recommended)
   - The project should be located in your XAMPP htdocs directory
   - Access the site at: `http://localhost/` (or your configured URL)

## Default Admin Account

- Email: admin@example.com
- Password: admin123

## Features Implemented

### Phase 1: Foundation Setup
- Basic MVC architecture
- Database setup with tables for users, auctions, lots, bids, and watchlist
- User authentication (login/register/logout)
- Homepage with basic layout
- Simple routing system

### Phase 2: Authentication & Basic Admin
- Admin dashboard with basic stats
- User management (view list, edit, delete)
- User dashboard for regular users
- User profile management
- Basic bid history and watchlist pages (UI only)

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

## Next Steps

- Auction management functionality
- Lot creation and management
- Bidding system implementation
- Watchlist functionality
- Countdown timers for auctions

## License

This project is for educational purposes only. Not for commercial use. 