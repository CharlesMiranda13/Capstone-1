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
    const unreadCountsUrl = document.querySelector('meta[name="unread-counts-url"]')?.content;
    
    if (!unreadCountsUrl) {
        console.log('No unread counts URL provided');
        return;
    }
    
    fetch(unreadCountsUrl)
        .then(response => response.json())
        .then(data => {
            // Update badges that exist on the page
            updateBadge('messages-badge', data.messages || 0);
            updateBadge('appointments-badge', data.appointments || 0);
            updateBadge('new-users-badge', data.new_users || 0);
            updateBadge('new-concerns-badge', data.new_concerns || 0);
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Initialize Pusher and notifications when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initial load - update badges on page load
    updateNotificationBadges();

    const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
    const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content;
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const userRole = document.querySelector('meta[name="user-role"]')?.content;

    // Check if Pusher config exists
    if (!pusherKey || !pusherCluster) {
        console.log('Pusher configuration not found');
        return;
    }

    // Set up Pusher for real-time updates
    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        encrypted: true
    });

    // Subscribe to appropriate channels based on user role
    if (userRole === 'admin') {
        // Admin subscribes to admin-notifications channel
        const adminChannel = pusher.subscribe('admin-notifications');

        // Listen for NewUserRegistered event
        adminChannel.bind('App\\Events\\NewUserRegistered', function(data) {
            console.log('New user registered:', data);
            updateNotificationBadges();
        });

        // Listen for NewConcernSubmitted event
        adminChannel.bind('App\\Events\\NewConcernSubmitted', function(data) {
            console.log('New concern submitted:', data);
            updateNotificationBadges();
        });
    } else if (userId) {
        // Other users subscribe to their personal channel
        const userChannel = pusher.subscribe('user.' + userId);

        // Listen for new message events
        userChannel.bind('new-message', function(data) {
            console.log('New message notification received:', data);
            updateBadge('messages-badge', data.unread_count);
        });

        // Listen for appointment updates
        userChannel.bind('appointment-update', function(data) {
            console.log('Appointment update notification received:', data);
            updateBadge('appointments-badge', data.appointment_count);
        });
    }

    // Connection status monitoring
    pusher.connection.bind('connected', function() {
        console.log('Real-time notifications connected');
    });

    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });
});