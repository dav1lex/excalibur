<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 mb-3">How to Bid</h1>
            <p class="lead text-muted">Learn how our auction platform works and how to place successful bids</p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h3 mb-4">Bidding Basics</h2>
                    
                    <p>Bidding on our platform is simple and straightforward. Here's what you need to know:</p>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary">
                            <i class="bi bi-1-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h5>Create an Account</h5>
                            <p>Before you can bid, you'll need to <a href="<?= BASE_URL ?>register">register for an account</a> or <a href="<?= BASE_URL ?>login">log in</a> if you already have one.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary">
                            <i class="bi bi-2-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h5>Browse Live Auctions</h5>
                            <p>Browse through our <a href="<?= BASE_URL ?>auctions">current auctions</a> and find items you're interested in. You can only place bids on auctions that are currently live.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary">
                            <i class="bi bi-3-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h5>Place Your Bid</h5>
                            <p>Enter your bid amount, which must be at least the minimum bid shown. You can also set a maximum bid for proxy bidding (explained below).</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary">
                            <i class="bi bi-4-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h5>Monitor Your Bids</h5>
                            <p>Keep track of your bids in your <a href="<?= BASE_URL ?>user/bids">My Bids</a> section. You'll be notified if you've been outbid.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-5-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <h5>Win the Auction</h5>
                            <p>If you're the highest bidder when the auction ends, congratulations! You've won the item.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h3 mb-4">Proxy Bidding System</h2>
                    
                    <p>Our platform uses a proxy bidding system, which allows you to set a maximum bid amount and let the system bid automatically on your behalf.</p>
                    
                    <div class="alert alert-light border mb-4">
                        <h5><i class="bi bi-info-circle-fill text-primary me-2"></i> How Proxy Bidding Works</h5>
                        <p class="mb-0">When you set a maximum bid, the system will only bid as much as necessary to outbid other users, up to your maximum amount. Your maximum bid is kept confidential from other bidders.</p>
                    </div>
                    
                    <h5 class="mb-3">Example:</h5>
                    <div class="bg-light p-4 rounded mb-4">
                        <ol class="mb-0">
                            <li>Current price of an item is <strong>10€</strong></li>
                            <li>You set a maximum bid of <strong>50€</strong></li>
                            <li>The system places a bid of <strong>10€</strong> for you, making you the highest bidder</li>
                            <li>Another user bids <strong>15€</strong></li>
                            <li>The system automatically bids <strong>20€</strong> on your behalf (using the appropriate bid increment)</li>
                            <li>This continues until either:
                                <ul>
                                    <li>Someone outbids your maximum of 50€</li>
                                    <li>The auction ends with you as the highest bidder</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                    
                    <div class="alert alert-success">
                        <strong><i class="bi bi-lightbulb-fill me-2"></i> Pro Tip:</strong> By using proxy bidding, you don't have to constantly monitor the auction. Set your true maximum price once and let the system do the work for you!
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h3 mb-4">Bid Increments</h2>
                    
                    <p>To ensure fair bidding, our system uses standardized bid increments based on the current price of the item:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Current Price Range</th>
                                    <th>Bid Increment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>0€ - 29€</td>
                                    <td>2€</td>
                                </tr>
                                <tr>
                                    <td>30€ - 99€</td>
                                    <td>5€</td>
                                </tr>
                                <tr>
                                    <td>100€ - 199€</td>
                                    <td>10€</td>
                                </tr>
                                <tr>
                                    <td>200€ - 499€</td>
                                    <td>20€</td>
                                </tr>
                                <tr>
                                    <td>500€ - 999€</td>
                                    <td>50€</td>
                                </tr>
                                <tr>
                                    <td>1000€ and above</td>
                                    <td>100€</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <p class="mt-3">For example, if the current bid is 25€, the minimum next bid would be 27€ (25€ + 2€ increment).</p>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h3 mb-4">Frequently Asked Questions</h2>
                    
                    <div class="accordion" id="bidFAQ">
                        <div class="accordion-item border mb-3">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Can I cancel a bid once it's placed?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#bidFAQ">
                                <div class="accordion-body">
                                    No, once a bid is placed, it cannot be canceled. Please bid carefully and only commit to amounts you're willing to pay.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border mb-3">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How do I know if I've been outbid?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#bidFAQ">
                                <div class="accordion-body">
                                    You can check the status of your bids in the "My Bids" section of your account. We recommend checking regularly during the final hours of an auction.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border mb-3">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What is a reserve price?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#bidFAQ">
                                <div class="accordion-body">
                                    A reserve price is the minimum amount the seller is willing to accept for an item. If the final bid does not meet the reserve price, the item will not be sold. The reserve price is not disclosed to bidders.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    What happens if I win an auction?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#bidFAQ">
                                <div class="accordion-body">
                                    If you win an auction, you'll be notified and can view your won items in your account dashboard. The auction house will contact you regarding payment and delivery arrangements.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto text-center">
            <h3 class="mb-4">Ready to Start Bidding?</h3>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="<?= BASE_URL ?>auctions" class="btn btn-primary btn-lg px-4">Browse Auctions</a>
                <a href="<?= BASE_URL ?>register" class="btn btn-outline-secondary btn-lg px-4">Create Account</a>
            </div>
        </div>
    </div>
</div> 