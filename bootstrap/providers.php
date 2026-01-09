<?php

return [
    App\Providers\AppServiceProvider::class,

    Modules\Support\SupportServiceProvider::class,
    Modules\AdminAuth\AdminAuthServiceProvider::class,
    Modules\User\UserServiceProvider::class,
    Modules\Dashboard\DashboardServiceProvider::class,
    Modules\AgentDashboard\Providers\AgentDashboardServiceProvider::class,
    Modules\CustomerDashboard\Providers\CustomerDashboardServiceProvider::class,
    Modules\Acl\AclServiceProvider::class,

    Modules\Blog\BlogServiceProvider::class,
    Modules\Flight\FlightServiceProvider::class,
    Modules\Hotel\HotelServiceProvider::class,
    Modules\Visa\VisaServiceProvider::class,
    Modules\Faq\FaqServiceProvider::class,
    Modules\Medical\MedicalServiceProvider::class,
    Modules\Tour\TourServiceProvider::class,
    Modules\Bill\BillServiceProvider::class,
    Modules\Employee\EmployeeServiceProvider::class,
    Modules\Customer\CustomerServiceProvider::class,
    Modules\Agent\AgentServiceProvider::class,
    Modules\Sms\SmsServiceProvider::class,
    Modules\Menu\MenuServiceProvider::class,
    Modules\Page\PageServiceProvider::class,
    Modules\Slider\SliderServiceProvider::class,
    Modules\Testimonial\TestimonialServiceProvider::class,
    Modules\Setting\SettingServiceProvider::class,

    Modules\Index\IndexServiceProvider::class,
];
