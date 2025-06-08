# NanoBid - A Simple Auction Platform

## Project Overview

NanoBid is a lightweight and user-friendly online auction platform built from the ground up with vanilla PHP. It is designed for auction houses that want to run their own timed online auctions without relying on expensive third-party services. The platform allows users to register, bid on items, and manage their activity, while providing administrators with the tools to manage the entire auction process. The core philosophy of the project was to create a simple, clean, and straightforward application, avoiding over-engineering and focusing on core functionality.

## Key Features

*   **User Management:** Secure user registration with email confirmation, login, and password reset functionality.
*   **Role-Based Access Control:** Distinct interfaces and permissions for regular users and administrators.
*   **Admin Dashboard:** A comprehensive dashboard for administrators to manage users, auctions, lots, and bids.
*   **Auction Management:** Full CRUD (Create, Read, Update, Delete) functionality for auctions, with support for different auction statuses (Draft, Upcoming, Live, Ended).
*   **Lot Management:** Administrators can create, update, and delete lots within an auction, including image uploads for each lot.
*   **Bidding System:** Users can place bids on lots. The system also includes a proxy bidding feature, where the system automatically bids on behalf of the user up to their maximum bid.
*   **Watchlist:** Users can add lots to a watchlist to easily track items they are interested in.
*   **Responsive UI:** A clean and professional user interface built with Bootstrap 5, ensuring a seamless experience across all devices.

## Technical Stack

*   **Backend:** PHP 8.0+
*   **Database:** MySQL with PDO for secure database operations.
*   **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5.
*   **Dependencies:** PHPMailer for sending emails (via Composer).
*   **Web Server:** Apache (running on XAMPP).

## Architectural Approach

The application is built using a custom Model-View-Controller (MVC) architecture. This separation of concerns makes the code more organized, maintainable, and scalable.

*   **Models:** Handle all the database interactions using PDO for prepared statements to prevent SQL injection vulnerabilities. Each database table has a corresponding model.
*   **Views:** Responsible for presenting the data to the user. They are written in a mix of HTML and PHP, with a focus on clean and semantic markup. Bootstrap 5 is used for styling.
*   **Controllers:** Act as the intermediary between the models and views. They receive user requests, fetch data from the models, and then pass that data to the appropriate view for rendering.
*   **Router:** A custom router (`index.php`) handles all incoming requests and directs them to the appropriate controller and method.

## Development Process

The development of NanoBid followed a methodical, step-by-step process. Each feature was built and tested individually before moving on to the next, ensuring a stable and reliable application. The project's roadmap was clearly defined and tracked in a `roadmap.md` file, which helped to maintain focus and track progress. The emphasis was always on writing clean, simple, and well-documented code.

## What I've Achieved

The project is now in its final stages. All the core features outlined in the roadmap have been successfully implemented and tested. This includes the entire user authentication system, the admin and user dashboards, complete auction and lot management, the bidding system with proxy bidding, and the user watchlist. The application is fully functional and provides a solid foundation for a real-world auction platform. The remaining tasks are focused on final touches, such as security enhancements and performance optimization, before deployment. 