# Auction Platform Development Guide

## Core Purpose
Help auction houses run their own timed online auctions without paying expensive third-party platforms.

## Tech Stack
- PHP 8.0+ (vanilla PHP with custom MVC - NO frameworks)
- MySQL with PDO
- Bootstrap 5 for UI
- Basic JavaScript for countdown timers and AJAX
- Composer for dependencies only: PHPMailer

## Key Requirements
- Simple, clean, straightforward design (no overengineering)
- Four auction states: draft → upcoming → live → ended
- Proxy bidding system
- Lot images (6MB max, JPG/PNG only)
- Admin and User panels
- Responsive Bootstrap UI

## Special Notes
- All prices are whole numbers (no decimal steps)
- Build step-by-step with testing at each phase
- Clean professional auction house look
- Security with PDO prepared statements
- Working on XAMPP in htdocs directory

## Development Process
1. Build one complete, testable step at a time
2. Test functionality before moving to next step
3. Focus on working code with clean Bootstrap UI 