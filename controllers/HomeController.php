<?php
require_once 'controllers/BaseController.php';

class HomeController extends BaseController {
    public function index() {
        // Get upcoming and live auctions
        //probably i dont use it rn?
        $auctionModel = new Auction();
        $upcomingAuctions = $auctionModel->getByStatus('upcoming', 4);
        $liveAuctions = $auctionModel->getByStatus('live', 4);
        
        $this->render('home/index', [
            'title' => SITE_NAME . ' - Online Auction Platform',
            'user' => $this->getCurrentUser(),
            'upcomingAuctions' => $upcomingAuctions,
            'liveAuctions' => $liveAuctions
        ]);
    }
    
    /**
     * Display the How to Bid page
     */
    public function howToBid()
    {
        $bidModel = new Bid();
        
        $this->render('home/how_to_bid', [
            'title' => 'How to Bid - ' . SITE_NAME,
            'user' => $this->getCurrentUser()
        ]);
    }
} 