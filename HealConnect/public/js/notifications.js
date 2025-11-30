function updateBadge(badgeId, count) {
    const badge = document.getElementById(badgeId);
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.add('show');
        } else {
            badge.classList.remove('show');
        }
    }
}

// Function to fetch and update all notification badges
function updateNotificationBadges() {
    const unreadCountsUrl = document.querySelector('meta[name="unread-counts-url"]').content;
    
    fetch(unreadCountsUrl)
        .then(response => response.json())
        .then(data => {
            updateBadge('messages-badge', data.messages);
            updateBadge('appointments-badge', data.appointments);
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Initialize Pusher and notifications when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initial load - update badges on page load
    updateNotificationBadges();

    // Get Pusher config from meta tags
    const pusherKey = document.querySelector('meta[name="pusher-key"]').content;
    const pusherCluster = document.querySelector('meta[name="pusher-cluster"]').content;
    const userId = document.querySelector('meta[name="user-id"]').content;

    // Set up Pusher for real-time updates
    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        encrypted: true
    });

    // Subscribe to user-specific channel
    const channel = pusher.subscribe('user.' + userId);

    // Listen for new message events
    channel.bind('new-message', function(data) {
        console.log('New message notification received:', data);
        updateBadge('messages-badge', data.unread_count);
    });

    // Listen for appointment updates
    channel.bind('appointment-update', function(data) {
        console.log('Appointment update notification received:', data);
        updateBadge('appointments-badge', data.appointment_count);
    });

    // Connection status monitoring
    pusher.connection.bind('connected', function() {
        console.log('Real-time notifications connected');
    });

    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });
});