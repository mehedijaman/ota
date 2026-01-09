export default {
    // Agent panel navigation - side menu
    items: [
        {
            label: 'Dashboard',
            icon: 'ri-dashboard-line',
            link: route('agentDashboard.index')
        },

        {
            label: 'Tour Bookings',
            icon: 'ri-calendar-check-line',
            children: [
                {
                    label: 'All Bookings',
                    icon: 'ri-list-check',
                    link: route('agentDashboard.index') // Placeholder - update when agent booking routes exist
                },
                {
                    label: 'Pending',
                    icon: 'ri-time-line',
                    link: route('agentDashboard.index') // Placeholder
                },
                {
                    label: 'Confirmed',
                    icon: 'ri-checkbox-circle-line',
                    link: route('agentDashboard.index') // Placeholder
                }
            ]
        },

        {
            label: 'My Customers',
            icon: 'ri-user-star-line',
            link: route('agentDashboard.index') // Placeholder - update when agent customer routes exist
        },

        {
            label: 'Earnings',
            icon: 'ri-wallet-3-line',
            children: [
                {
                    label: 'Overview',
                    icon: 'ri-pie-chart-line',
                    link: route('agentDashboard.index') // Placeholder
                },
                {
                    label: 'Commission History',
                    icon: 'ri-exchange-funds-line',
                    link: route('agentDashboard.index') // Placeholder
                }
            ]
        },

        {
            label: 'My Profile',
            icon: 'ri-user-settings-line',
            link: route('agentDashboard.index') // Placeholder - update when agent profile routes exist
        }
    ]
}
