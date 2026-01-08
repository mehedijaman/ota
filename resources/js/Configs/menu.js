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
            label: 'Customers',
            permission: 'customer',
            icon: 'ri-user-star-line',
            children: [
                {
                    label: 'Customer List',
                    permission: 'customer-list',
                    icon: 'ri-contacts-line',
                    link: route('customer.index')
                },
                {
                    label: 'New Customer',
                    permission: 'customer-create',
                    icon: 'ri-user-add-line',
                    link: route('customer.create')
                }
            ]
        },

        {
            label: 'Agents',
            permission: 'agent',
            icon: 'ri-building-line',
            children: [
                {
                    label: 'Agent List',
                    permission: 'agent-list',
                    icon: 'ri-building-2-line',
                    link: route('agent.index')
                },
                {
                    label: 'New Agent',
                    permission: 'agent-create',
                    icon: 'ri-add-circle-line',
                    link: route('agent.create')
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
            label: 'Website',
            permission: 'website',
            icon: 'ri-global-line',
            children: [
                {
                    label: 'Pages',
                    permission: 'website-page-list',
                    icon: 'ri-file-list-line',
                    link: route('page.index')
                },
                {
                    label: 'Menus',
                    permission: 'website-menu-list',
                    icon: 'ri-file-list-line',
                    link: route('menu.index')
                },
                
                {
                    label: 'Sliders',
                    permission: 'website-slider-list',
                    icon: 'ri-file-list-line',
                    link: route('slider.index')
                },

                {
                    label: 'Testimonials',
                    permission: 'website-testimonial-list',
                    icon: 'ri-file-list-line',
                    link: route('testimonial.index')
                },
                
                {
                    label: 'Settings',
                    permission: 'website-setting-list',
                    icon: 'ri-file-list-line',
                    link: route('setting.index')
                },
            ]
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
            label: 'FAQ',
            permission: 'faq',
            icon: 'ri-question-answer-line',
            children: [
                {
                    label: 'FAQs',
                    permission: 'faq-list',
                    icon: 'ri-questionnaire-line',
                    link: route('faq.index')
                },
                {
                    label: 'New FAQ',
                    permission: 'faq-create',
                    icon: 'ri-add-circle-line',
                    link: route('faq.create')
                },
                {
                    label: 'Categories',
                    permission: 'faq-category-list',
                    icon: 'ri-folders-line',
                    link: route('faqCategory.index')
                }
            ]
        },

        {
            label: 'SMS',
            permission: 'sms',
            icon: 'ri-message-2-line',
            children: [
                {
                    label: 'Send SMS',
                    permission: 'sms-send',
                    icon: 'ri-send-plane-line',
                    link: route('sms.send.create')
                },
                {
                    label: 'Campaigns',
                    permission: 'sms-campaign-list',
                    icon: 'ri-megaphone-line',
                    link: route('sms.campaigns.index')
                },
                {
                    label: 'Templates',
                    permission: 'sms-template-list',
                    icon: 'ri-file-text-line',
                    link: route('sms.templates.index')
                },
                {
                    label: 'Gateways',
                    permission: 'sms-gateway-list',
                    icon: 'ri-server-line',
                    link: route('sms.gateways.index')
                }
            ]
        },

        {
            label: 'User & ACL',
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
        }
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
