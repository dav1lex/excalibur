# NanoBid Development Roadmap

## Phase 1: Foundation Setup
- [x] Database schema creation
- [x] Basic MVC structure implementation
- [x] Router system
- [x] Homepage
- [x] Login/Register pages

## Phase 2: Authentication & Basic Admin
- [x] User authentication system  = wors
- [x] Session management = works
- [x] Admin dashboard (basic) = works
- [x] User management (view, edit, delete) =for admin, yes works
- [x] Role-based navigation (admin vs user) =yes

## Phase 3: Auction Management
- [x] Auction CRUD operations
- [x] Auction state management
- [x] Lots management
- [x] Image upload for lots
- [x] Admin auction management interface
- [x] Admin lot management interface
- [x] Fixed routing issues for auction and lot management
- [x] Fixed lot creation and update functionality
- [x] Improved UI for lot management with auction filtering

## Phase 4: Bidding System
- [x] Bid placement functionality 
- [x] Proxy bidding system 
- [x] Watchlist functionality 
- [x] Countdown timers
- [x] Bid history display
- [x] User's bid history page

## Phase 5: User Experience
- [x] User dashboard
- [x] My bids history
- [x] Won items tracking
- [x] Email notifications for registration
- [ SKIP THIS] Email notifications for bidding events 
- [x] Role-specific dashboard access
- [x] Watchlist management

## Phase 6: Final Touches
- [x] Responsive UI with Bootstrap 5
- [ ] Security enhancements
- [ ] Performance optimization
- [ ] Testing and bug fixes

## Fixed Issues
- [x] Dashboard button in header now shows appropriate link based on user role
- [x] Added missing admin/lots route and controller method
- [x] Fixed create lot functionality to properly redirect
- [x] Fixed auction edit URL handling
- [x] Implemented proper controllers and models for auction and lot management
- [x] Fixed PDO binding issues in lot creation
- [x] Improved navigation flow between auction and lot management
- [x] Streamlined sidebar navigation by removing redundant links
- [x] Added auction filtering in lots management page
- [x] Implemented bidding system with proxy bidding
- [x] Added watchlist functionality for users
- [x] Fixed missing admin/bids view and controller
- [x] Fixed auction status updates to work automatically
- [x] Fixed visibility of draft auctions (now hidden from public view)
- [x] Fixed PDO binding in updateStatuses method
- [x] Resolved issue with lot counts on admin auction page
- [x] Fixed auction status not changing due to routing and controller logic
- [x] Corrected lot update functionality (image, price, etc.) and redirect behavior 
- [x] Added email confirmation for user registration 