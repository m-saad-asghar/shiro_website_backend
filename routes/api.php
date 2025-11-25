<?php

use App\Http\Controllers\Api\Auth\AuthUserController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserVerifyEmailController;
use App\Http\Controllers\Api\Property\AgentDeveloperController;
use App\Http\Controllers\Api\Property\PropertyController;
use App\Http\Controllers\Api\Property\ServiceController;
use App\Http\Controllers\Api\Property\TypeController;
use App\Http\Controllers\Api\StaticPage\StaticController;
use App\Http\Controllers\Api\User\FavouriteController;
use App\Http\Controllers\Api\User\Payment\StripePaymentController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\UserPaymentController;
use App\Http\Controllers\Api\Property\PropertyLeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


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

Route::post('/contact/submit', [UserController::class, 'submitContactForm']);
Route::post('/subscribe', [UserController::class, 'store']);

Route::get('/properties/filters', [PropertyController::class, 'getFilterOptions']);
Route::post('/properties/search', [PropertyController::class, 'searchProperty']);
Route::post('/properties/search-by-location', [PropertyController::class, 'searchByLocation']);
Route::get('/properties', [PropertyController::class, 'allProperties']);
Route::get('/property/show', [PropertyController::class, 'show']);
Route::get('/property/slug/{slug}', [PropertyController::class, 'showBySlug']);

Route::post('/contact-agent', [UserController::class, 'submitContactAgentForm']);


Route::get('/agents', [AgentDeveloperController::class, 'allAgents']);
Route::get('/developers', [AgentDeveloperController::class, 'allDevelopers']);
Route::get('show/developer', [AgentDeveloperController::class, 'show']);

Route::get('/services', [ServiceController::class, 'allServices']);
Route::get('show/services', [ServiceController::class, 'showService']);
Route::get('/types', [TypeController::class, 'allTypes']);

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



