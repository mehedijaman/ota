export default {
    // main navigation - side menu
    items: [
        {
            label: 'Dashboard',
            permission: 'dashboard',
            icon: 'ri-dashboard-line',
            link: route('dashboard.index')
        },

        {
            label: 'Blog',
            permission: 'blog',
            icon: 'ri-book-2-line',
            children: [
                {
                    label: 'Posts',
                    permission: 'blog-post-list',
                    icon: 'ri-draft-line',
                    link: route('blogPost.index')
                },
                {
                    label: 'Categories',
                    permission: 'blog-category-list',
                    icon: 'ri-folders-line',
                    link: route('blogCategory.index')
                },
                {
                    label: 'Tags',
                    permission: 'blog-tag-list',
                    icon: 'ri-price-tag-3-line',
                    link: route('blogTag.index')
                },
                {
                    label: 'Authors',
                    permission: 'blog-author-list',
                    icon: 'ri-team-line',
                    link: route('blogAuthor.index')
                }
            ]
        },

        {
            label: 'Employee',
            permission: 'employee',
            icon: 'ri-book-2-line',
            children: [
                {
                    label: 'Employee List',
                    permission: 'employee-list',
                    icon: 'ri-user-line',
                    link: route('employee.index')
                },
                {
                    label: 'New Employee',
                    permission: 'employee-create',
                    icon: 'ri-user-add-line',
                    link: route('employee.create')
                },
                {
                    label: 'Designations',
                    permission: 'employee-designation-list',
                    icon: 'ri-price-tag-3-line',
                    link: route('designation.index')
                },
                {
                    label: 'Departments',
                    permission: 'employee-department-list',
                    icon: 'ri-team-line',
                    link: route('department.index')
                },
                {
                    label: 'Areas',
                    permission: 'employee-area-list',
                    icon: 'ri-price-tag-3-line',
                    link: route('area.index')
                }
            ]
        },

        {
            label: 'Access Control List',
            permission: 'acl',
            icon: 'ri-shield-user-line',
            children: [
                {
                    label: 'Users',
                    permission: 'acl-user-list',
                    icon: 'ri-user-line',
                    link: route('user.index')
                },
                {
                    label: 'Permissions',
                    permission: 'acl-permission-list',
                    icon: 'ri-shield-keyhole-line',
                    link: route('aclPermission.index')
                },
                {
                    label: 'Roles',
                    permission: 'acl-role-list',
                    icon: 'ri-account-box-line',
                    link: route('aclRole.index')
                }
            ]
        },
        // {
        //     label: 'Maintenance',
        //     permission: 'maintenance',
        //     icon: 'ri-settings-2-line',
        //     children: [
        //         {
        //             label: 'Log Viewer',
        //             permission: 'maintenance-log-viewer',
        //             icon: 'ri-user-line',
        //             link: route('logViewer.index')
        //         },
        //         // {
        //         //     label: 'Backup',
        //         //     permission: 'maintenance-backup-list',
        //         //     icon: 'ri-shield-keyhole-line',
        //         //     link: route('backup.index')
        //         // },
        //     ]
        // }
    ]
}
