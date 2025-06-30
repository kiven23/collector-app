<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => \App\Http\Middleware\AdminAuth::class,
        'clientconnection' => \App\Http\Middleware\ClientConnection::class,
        'clearance' => \App\Http\Middleware\ClearanceMiddleware::class,

        // Service Call
        // computerware
        'product_category_clearance' => \App\Http\Middleware\ProductCategoryClearance::class,
        'product_brand_clearance' => \App\Http\Middleware\ProductBrandClearance::class,
        'product_item_clearance' => \App\Http\Middleware\ProductItemClearance::class,
        'computerware_ticket_clearance' => \App\Http\Middleware\ComputerwareTicketClearance::class,
        'power_interruption_clearance' => \App\Http\Middleware\PowerInterruptionClearance::class,
        // connectivity
        'service_category_clearance' => \App\Http\Middleware\ServiceCategoryClearance::class,
        'service_provider_clearance' => \App\Http\Middleware\ServiceProviderClearance::class,
        'service_type_clearance' => \App\Http\Middleware\ServiceTypeClearance::class,
        'connectivity_ticket_clearance' => \App\Http\Middleware\ConnectivityTicketClearance::class,

        // Inventory
        'inventory_clearance' => \App\Http\Middleware\InventoryClearance::class,

        // Message Cast
        'message_cast_setting_clearance' => \App\Http\Middleware\MessageCastSettingClearance::class,
        'contact_list_clearance' => \App\Http\Middleware\ContactListClearance::class,
        'message_clearance' => \App\Http\Middleware\MessageClearance::class,

        // Customer Photo
        'customer_clearance' => \App\Http\Middleware\CustomerClearance::class,

        // Pending Transaction Monitoring
        'pending_clearance' => \App\Http\Middleware\PendingClearance::class,

        // Interview Schedule
        'interview_schedules_clearance' => \App\Http\Middleware\InterviewScheduleClearance::class,

        // Report : Biometric & DTR
        'report_clearance' => \App\Http\Middleware\ReportClearance::class,

        // Employee
        'employee_clearance' => \App\Http\Middleware\EmployeeClearance::class,

        // User
        'user_clearance' => \App\Http\Middleware\UserClearance::class,

        // User Employment
        'user_employment_clearance' => \App\Http\Middleware\UserEmploymentClearance::class,

        // User Authorization
        'user_auth_clearance' => \App\Http\Middleware\UserAuthorizationClearance::class,

        // Role
        'role_clearance' => \App\Http\Middleware\RoleClearance::class,

        // Permission
        'permission_clearance' => \App\Http\Middleware\PermissionClearance::class,

        // Branch
        'branch_clearance' => \App\Http\Middleware\BranchClearance::class,

        // Branch Schedule
        'branch_sched_clearance' => \App\Http\Middleware\BranchScheduleClearance::class,

        // Region
        'region_clearance' => \App\Http\Middleware\RegionClearance::class,

        // Division
        'division_clearance' => \App\Http\Middleware\DivisionClearance::class,

        // Department
        'department_clearance' => \App\Http\Middleware\DepartmentClearance::class,

        // Position
        'position_clearance' => \App\Http\Middleware\PositionClearance::class,

        // AccessChart
        'access_chart_clearance' => \App\Http\Middleware\AccessChartClearance::class,

        // OTLOA Approval
        'otloa_approval_clearance' => \App\Http\Middleware\OtloaApprovalClearance::class,

        // Overtime
        'overtime_clearance' => \App\Http\Middleware\OvertimeClearance::class,

        // Maintenance Request
        'maint_request_clearance' => \App\Http\Middleware\MaintRequestClearance::class,

        // Maintenance Request Approval
        'mrf_approval_clearance' => \App\Http\Middleware\MrfApprovalClearance::class,

        // Company
        'company_clearance' => \App\Http\Middleware\CompanyClearance::class,

        // PO File
        'po_file_clearance' => \App\Http\Middleware\PurchaseOrderFileClearance::class,

        // PO File Approvel
        'po_file_approval_clearance' => \App\Http\Middleware\PoFileApprovalClearance::class,

        // Concern
        'concern_clearance' => \App\Http\Middleware\ConcernClearance::class,
        // Concern Type
        'concern_type_clearance' => \App\Http\Middleware\ConcernTypeClearance::class,
        // Concern Category
        'concern_category_clearance' => \App\Http\Middleware\ConcernCategoryClearance::class,

        // Purchasing Report
        'purch_report_clearance' => \App\Http\Middleware\PurchasingReportClearance::class,

        // File Type
        'file_type_clearance' => \App\Http\Middleware\FileTypeClearance::class,

        // Announcement
        'announcement_clearance' => \App\Http\Middleware\AnnouncementClearance::class,

        // Agenies & Archived
        'government_clearance' => \App\Http\Middleware\GovernMent::class,

        // Agenies & Archived
        'cdr_clearance' => \App\Http\Middleware\CreditCollections::class,

        // Sap Api
        'sapapi_clearance' => \App\Http\Middleware\SapApi::class,

        //Validation Portal 
        'validation_portal_clearance' => \App\Http\Middleware\ValidationPortalClearance::class,

        // Gift Code
        'gift_code' => \App\Http\Middleware\GiftCode::class,

         // Settings
         'settings' => \App\Http\Middleware\Settings::class,

          // Settings
          'sale_edge' => \App\Http\Middleware\SalesEdge::class,
    ];
}
