<?php

return [
    App\Providers\AppServiceProvider::class,

    Modules\Support\SupportServiceProvider::class,
    Modules\AdminAuth\AdminAuthServiceProvider::class,
    Modules\User\UserServiceProvider::class,
    Modules\Dashboard\DashboardServiceProvider::class,
    Modules\Acl\AclServiceProvider::class,

    Modules\Index\IndexServiceProvider::class,
    Modules\Blog\BlogServiceProvider::class,
    Modules\Flight\FlightServiceProvider::class,
    Modules\Hotel\HotelServiceProvider::class,
    Modules\Holiday\HolidayServiceProvider::class,
    Modules\Visa\VisaServiceProvider::class,
    Modules\BillPayment\BillPaymentServiceProvider::class,
    Modules\Faq\FaqServiceProvider::class,
    Modules\Medical\MedicalServiceProvider::class,
    Modules\Tour\TourServiceProvider::class,
    Modules\Bill\BillServiceProvider::class,
    Modules\Employee\EmployeeServiceProvider::class,
    Modules\Customer\CustomerServiceProvider::class,
    Modules\Agent\AgentServiceProvider::class,
    Modules\Sms\SmsServiceProvider::class,
];
