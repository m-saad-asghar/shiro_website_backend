<?php

use App\Http\Controllers\Api\Auth\AuthUserController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserVerifyEmailController;
use App\Http\Controllers\Api\Property\AgentDeveloperController;
use App\Http\Controllers\Api\Property\PropertyController;
use App\Http\Controllers\Api\Property\ListingController;
use App\Http\Controllers\Api\Property\ServiceController;
use App\Http\Controllers\Api\Property\TypeController;
use App\Http\Controllers\Api\StaticPage\StaticController;
use App\Http\Controllers\Api\User\FavouriteController;
use App\Http\Controllers\Api\User\Payment\StripePaymentController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\UserPaymentController;
use App\Http\Controllers\Api\Property\PropertyLeadController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\ListingSyncController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\NewUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
     Route::get('me', [AdminAuthController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::post('users', [AdminAuthController::class, 'store']);
        Route::get('fetch_users', [NewUserController::class, 'index']); 
        Route::get('fetch_developers', [DeveloperController::class, 'fetchDevelopers']);
         Route::get('fetch_employees', [EmployeeController::class, 'fetchEmployees']);
        Route::post('create_developer', [DeveloperController::class, 'createDeveloper']);
        Route::get('get_developer', [DeveloperController::class, 'getDeveloper']);
        Route::post('change_status_developer', [DeveloperController::class, 'changeStatusDeveloper']); 
        Route::post('change_status_employee', [EmployeeController::class, 'changeStatusEmployee']);
        Route::get('fetch_position', [EmployeeController::class, 'fetchPosition']);
        Route::get('fetch_departments', [EmployeeController::class, 'fetchDepartments']);
        Route::get('fetch_crm_agents', [EmployeeController::class, 'fetchCrmAgents']);
        Route::post('create_employee', [EmployeeController::class, 'createEmployee']);
        Route::post('update_employee', [EmployeeController::class, 'updateEmployee']);
        Route::get('get_employee', [EmployeeController::class, 'getEmployee']);
        Route::post('update_developer', [DeveloperController::class, 'updateDeveloper']);
        Route::post('add_user', [NewUserController::class, 'store']);
        Route::post('/delete_user', [NewUserController::class, 'delete_user']);
        Route::post('update_user', [NewUserController::class, 'update_user']);
        Route::post('change_status_user', [NewUserController::class, 'changeStatusUser']);
        Route::get('roles', [AdminAuthController::class, 'get_roles']);
        Route::post('delete_role', [AdminAuthController::class, 'delete_role']);
        Route::post('add-role', [AdminAuthController::class, 'add_role']);
        Route::post('update-role', [AdminAuthController::class, 'update_role']);
        Route::post('/role-permissions/update', [PermissionsController::class, 'updateRolePermissions']);
        Route::post('/role-permissions', [PermissionsController::class, 'storeRolePermissions']);
        Route::get('permissions', [PermissionsController::class, 'get_permissions']);
        Route::get('all-permissions', [PermissionsController::class, 'all_permissions']);
        Route::post('delete_permission', [PermissionsController::class, 'delete_permission']);
        Route::post('update-permission', [PermissionsController::class, 'update_permission']);
        Route::get('fetch_all_listings', [ListingController::class, 'fetch_all_listings']);
        Route::post('change_status_listing', [ListingController::class, 'changeStatusListing']);
        Route::get('fetch_all_projects', [ProjectController::class, 'fetchAllProjects']);
        Route::post('save_project', [ProjectController::class, 'saveProject']);
        Route::post('change_status_project', [ProjectController::class, 'changeStatusProject']);
        Route::get('fetch_amenities', [ProjectController::class, 'amenitiesIndex']);
        Route::get('fetch_communities', [ProjectController::class, 'fetchCommunities']);
        Route::get('fetch_developers_dropdown', [ProjectController::class, 'fetchDevelopersDropdown']);
        Route::get('projects/{project_id}/edit-data', [ProjectController::class, 'editData']);
        Route::post('projects/{id}/update', [ProjectController::class, 'updateProject']);
        Route::get('amenities', [ProjectController::class, 'fetchAmenities']);
        Route::post('add-amenity', [ProjectController::class, 'add_amenity']);
        Route::put('amenities/{id}', [ProjectController::class, 'update_amenity']);
        Route::delete('amenities/{id}', [ProjectController::class, 'delete_amenity']);
        Route::post('change_status_amenity', [ProjectController::class, 'changeStatusAmenity']);
        Route::get('communities', [CommunityController::class, 'fetchCommuntiesForAdminPanel']); 
        Route::post('add-community', [CommunityController::class, 'add_community']);
        Route::put('communities/{id}', [CommunityController::class, 'update_community']);
        Route::delete('communities/{id}', [CommunityController::class, 'delete_community']);
        Route::post('change_status_communities', [CommunityController::class, 'changeStatusCommunity']);
        Route::middleware('auth:api')->group(function () {
        // Route::get('me', [AdminAuthController::class, 'me']);
        // Route::post('logout', [AdminAuthController::class, 'logout']);
        // Route::post('users', [AdminAuthController::class, 'store']);
        // Route::get('fetch_users', [NewUserController::class, 'index']); 
        // Route::get('fetch_developers', [DeveloperController::class, 'fetchDevelopers']);
        // Route::post('create_developer', [DeveloperController::class, 'createDeveloper']);
        // Route::get('get_developer', [DeveloperController::class, 'getDeveloper']);
        // Route::post('change_status_developer', [DeveloperController::class, 'changeStatusDeveloper']); 
        // Route::post('update_developer', [DeveloperController::class, 'updateDeveloper']);
        // Route::post('add_user', [NewUserController::class, 'store']);
        // Route::post('/delete_user', [NewUserController::class, 'delete_user']);
        // Route::post('update_user', [NewUserController::class, 'update_user']);
        // Route::post('change_status_user', [NewUserController::class, 'changeStatusUser']);
        // Route::get('roles', [AdminAuthController::class, 'get_roles']);
        // Route::post('delete_role', [AdminAuthController::class, 'delete_role']);
        // Route::post('add-role', [AdminAuthController::class, 'add_role']);
        // Route::post('update-role', [AdminAuthController::class, 'update_role']);
        // Route::post('/role-permissions/update', [PermissionsController::class, 'updateRolePermissions']);
        // Route::post('/role-permissions', [PermissionsController::class, 'storeRolePermissions']);
        // Route::get('permissions', [PermissionsController::class, 'get_permissions']);
        // Route::get('all-permissions', [PermissionsController::class, 'all_permissions']);
        // Route::post('delete_permission', [PermissionsController::class, 'delete_permission']);
        // Route::post('update-permission', [PermissionsController::class, 'update_permission']);
        // Route::get('fetch_all_listings', [ListingController::class, 'fetch_all_listings']);
        // Route::post('change_status_listing', [ListingController::class, 'changeStatusListing']);
        // Route::get('fetch_all_projects', [ProjectController::class, 'fetchAllProjects']);
        // Route::post('save_project', [ProjectController::class, 'saveProject']);
        // Route::post('change_status_project', [ProjectController::class, 'changeStatusProject']);
        // Route::get('fetch_amenities', [ProjectController::class, 'amenitiesIndex']);
        // Route::get('fetch_communities', [ProjectController::class, 'fetchCommunities']);
        // Route::get('fetch_developers_dropdown', [ProjectController::class, 'fetchDevelopersDropdown']);
        // Route::get('projects/{project_id}/edit-data', [ProjectController::class, 'editData']);
        // Route::post('projects/{id}/update', [ProjectController::class, 'updateProject']);
     
       
    });
});


Route::prefix('static')->controller(StaticController::class)->group(function () {
    Route::get('privacy', 'allPrivacy');
    Route::get('terms-condition', 'allTermsAndCondition');
    Route::get('faqs', 'allFaqs');
    Route::get('faqs/show', 'showFaq');
    Route::get('sliders', 'allSliders');
    Route::get('contact-info', 'contactInfo');
    Route::get('team', 'allTeam');
    Route::get('team/management', 'managementTeam');
    Route::get('team/brokers', 'brokersTeam');
    Route::get('show/team','showTeam');
    Route::get('about-us', 'allAboutUs');
    Route::get('currency', 'allCurrency');
    Route::get('region', 'allRegion');
    Route::get('reviews', 'allReviews');
});
Route::post('/send-email-notification', [NotificationController::class, 'sendEmail']);
Route::post('/contact/submit', [UserController::class, 'submitContactForm']);
Route::post('/form_submission', [UserController::class, 'formSubmission']);
Route::post('/form_submission_get_a_call', [UserController::class, 'formSubmissionGetACall']);
Route::post('/subscribe', [UserController::class, 'store']);

Route::get('/download-brochure', [ProjectController::class, 'downloadBrochure']);

// Route::get('/download-brochure', function () {
//     return response()->download(
//         storage_path('app/public/projects/Shiro Estate Creek Bay Brochure.pdf'),
//         'Shiro Estate Creek Bay Brochure.pdf'
//     );
// });

Route::get('/listing_details/{reference}', [PropertyController::class, 'listingDetails']);
Route::get('/show_featured_properties', [PropertyController::class, 'showFeaturedProperties']);
Route::get(
    '/show_featured_properties_with_type',
    [PropertyController::class, 'showFeaturedPropertiesWithType']
);
Route::get('/show_sale_properties', [PropertyController::class, 'showSaleProperties']);
Route::get('/show_offplan_properties', [PropertyController::class, 'showOffplanProperties']);
Route::get('/show_rent_properties', [PropertyController::class, 'showRentProperties']);
Route::get('/properties/filters', [PropertyController::class, 'getFilterOptions']);
Route::post('/properties/search', [PropertyController::class, 'searchProperty']);
Route::post('/properties/search-by-location', [PropertyController::class, 'searchByLocation']);
Route::get('/properties', [PropertyController::class, 'allProperties']);
Route::get('/property/show', [PropertyController::class, 'show']);
Route::get('/property/slug/{slug}', [PropertyController::class, 'showBySlug']);
Route::get('/get_listing_options', [ListingController::class, 'showListingsOptions']);
Route::get('/fetch_property_types', [PropertyController::class, 'fetchPropertyTypes']);
// routes/api.php
Route::get('/resolve_search_slugs', [ListingController::class, 'resolveSearchSlugs']);
Route::post('/contact-agent', [UserController::class, 'submitContactAgentForm']);
Route::get('/fetch_developer_data/{slug}', [DeveloperController::class, 'show']);
Route::get('/fetch_community_data/{slug}', [CommunityController::class, 'show']);
Route::get('/fetch_projects_from_developer/{slug}', [DeveloperController::class, 'projectsFromDeveloper']);
Route::get('/fetch_all_projects', [DeveloperController::class, 'fetchAllProjects']);
Route::get('/fetch_project_details/{slug}', [ProjectController::class, 'show']);
Route::get(
    '/fetch_projects_from_community',
    [DeveloperController::class, 'projectsFromCommunity']
);
Route::get('/agents', [AgentDeveloperController::class, 'allAgents']);
Route::get('/developers', [AgentDeveloperController::class, 'allDevelopers']);
Route::get('show/developer', [AgentDeveloperController::class, 'show']);

Route::get('/areas', [AreaController::class, 'allAreas']);
Route::get('/fetch_area_details/{slug}', [AreaController::class, 'fetchAreaDetails']);
Route::get('/listings_by_slug', [ListingController::class, 'listingsBySlug']);

Route::get('/services', [ServiceController::class, 'allServices']);
Route::get('show/services', [ServiceController::class, 'showService']);
Route::get('/types', [TypeController::class, 'allTypes']);

Route::get('/fetch_employees', [EmployeeController::class, 'show']);
Route::get('/fetch_employee_details/{slug}', [EmployeeController::class, 'showDetails']);
Route::get('/fetch_message_from_founder', [EmployeeController::class, 'messageFromFounder']);
Route::get('/fetch_about_us_content', [EmployeeController::class, 'fetchAboutUsContent']);

// Syncing Routes

Route::get('/sync-property-types', [IntegrationController::class, 'syncPropertyTypes']);
Route::get('/sync-communities', [IntegrationController::class, 'syncCommunities']);
Route::get('/sync-sub-communities', [IntegrationController::class, 'syncSubCommunities']);
Route::get('/sync-listing-developers', [IntegrationController::class, 'syncListingDevelopers']);
Route::get('/sync-locations', [IntegrationController::class, 'syncLocations']);
Route::get('/sync-cities', [IntegrationController::class, 'syncCities']);
Route::get('/sync-agents', [IntegrationController::class, 'syncAgents']);
Route::get('/sync-private-amenities', [IntegrationController::class, 'syncPrivateAmenities']);
Route::get('/sync-commercial-amenities', [IntegrationController::class, 'syncCommercialAmenities']);
Route::get('/sync-properties', [IntegrationController::class, 'syncProperties']);
Route::get('/sync/listings', [ListingSyncController::class, 'sync']);


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthUserController::class, 'login']);
    Route::post('register', [AuthUserController::class, 'register']);
    Route::post('check-email', [AuthUserController::class, 'checkEmail']);
    Route::post('email-exists', [AuthUserController::class, 'checkEmailExists']);

    Route::post('forgot-password', [ResetPasswordController::class, 'forgot_password']);
    Route::post('check-reset-token', [ResetPasswordController::class, 'checkToken']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
});


Route::middleware('auth:sanctum')->prefix('favourites')->group(function () {
    Route::get('/', [FavouriteController::class, 'index']);        
    Route::get('check', [FavouriteController::class, 'show']);          
    Route::post('toggle', [FavouriteController::class, 'toggle']);       
});

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);                
    Route::post('profile/update', [UserController::class, 'updateProfile']);          
    Route::post('profile/upload', [UserController::class, 'uploadProfilePicture']);   
    Route::post('change-password', [UserController::class, 'changePassword']);         
});


// 📰 Blog and Articles
Route::get('/blog-categories', [StaticController::class, 'allBlogCategories']); // List all blog categories (optional with blogs)
Route::get('/blogs', [StaticController::class, 'allBlogs']); // List all blogs (optional by category ID)
Route::get('/blogs/show', [StaticController::class, 'showBlog']); // Show specific blog post by ID


Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('all/payments', [UserPaymentController::class, 'index']);
    Route::group(['prefix' => 'payment'], function () {
        Route::post('/attach_method', [StripePaymentController::class, 'attachPaymentMethod']);
        Route::get('/user_methods', [StripePaymentController::class, 'getUserPaymentMethods']);
        Route::post('/detach_method', [StripePaymentController::class, 'detach_payment']);
    });
});

Route::prefix('user')->group(function () {
    Route::post('/resend-email-otp', [UserVerifyEmailController::class, 'resendVerificationOtp']);
    Route::post('/verify-email-otp', [UserVerifyEmailController::class, 'verifyEmailOtp']);
});

// Property Leads (Interest Registration)
Route::post('/property/register-interest', [PropertyLeadController::class, 'store']);

// Protected routes for leads management
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/property/leads', [PropertyLeadController::class, 'index']);
    Route::patch('/property/leads/{lead}/status', [PropertyLeadController::class, 'updateStatus']);
});



