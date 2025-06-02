document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Update auction countdown timers
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
    
    // Setup bid form validation
    setupBidValidation();
});

// Function to update all countdown timers on the page
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(function(element) {
        const endTime = new Date(element.getAttribute('data-end-time')).getTime();
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            element.innerHTML = "Auction Ended";
            element.classList.add('text-danger');
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let countdownText = '';
        
        if (days > 0) countdownText += days + "d ";
        countdownText += hours + "h " + minutes + "m " + seconds + "s";
        
        element.innerHTML = countdownText;
    });
}

// Function to setup bid form validation
function setupBidValidation() {
    const bidForms = document.querySelectorAll('.bid-form');
    
    bidForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const bidInput = form.querySelector('.bid-input');
            const minBid = parseInt(bidInput.getAttribute('data-min-bid'));
            const bidAmount = parseInt(bidInput.value);
            
            if (isNaN(bidAmount) || bidAmount < minBid) {
                event.preventDefault();
                alert('Please enter a valid bid amount. Minimum bid: $' + minBid);
            }
        });
    });
}

// Function to toggle watchlist item
function toggleWatchlist(lotId, element) {
    fetch('watchlist/toggle/' + lotId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.classList.toggle('fas');
            element.classList.toggle('far');
        }
    })
    .catch(error => console.error('Error:', error));
} 