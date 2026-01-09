export default {
    // Customer panel navigation - side menu
    items: [
        {
            label: 'Dashboard',
            icon: 'ri-dashboard-line',
            link: route('customerDashboard.index')
        },

        {
            label: 'My Bookings',
            icon: 'ri-calendar-check-line',
            link: route('customerDashboard.index') // Placeholder - update when booking routes exist
        },

        {
            label: 'My Profile',
            icon: 'ri-user-line',
            link: route('customerDashboard.index') // Placeholder - update when profile routes exist
        },

        {
            label: 'Payment History',
            icon: 'ri-wallet-3-line',
            link: route('customerDashboard.index') // Placeholder
        },

        {
            label: 'Support',
            icon: 'ri-customer-service-2-line',
            link: route('customerDashboard.index') // Placeholder
        }
    ]
}
