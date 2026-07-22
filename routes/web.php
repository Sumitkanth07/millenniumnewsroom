<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdPlacementController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\CalculatorSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\RedirectController as AdminRedirectController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdvertisementController;
use Illuminate\Support\Facades\Route;

Route::get('/ads/track-view/{id}', [AdvertisementController::class, 'trackView'])->name('ads.track-view');
Route::get('/ads/click/{id}', [AdvertisementController::class, 'trackClick'])->name('ads.track-click');

Route::get('/google1104d3cf9cd3b0b9.html', function () {
    $path = public_path('google1104d3cf9cd3b0b9.html');
    if (!file_exists($path)) {
        abort(404);
    }
    return response(file_get_contents($path), 200, ['Content-Type' => 'text/html']);
});

Route::get('/ads.txt', function () {
    $path = public_path('ads.txt');
    if (!file_exists($path)) {
        $path = base_path('ads.txt');
    }
    if (!file_exists($path)) {
        abort(404);
    }
    return response(file_get_contents($path), 200, ['Content-Type' => 'text/plain']);
});

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/category/{category:slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/categories', [FrontendController::class, 'allCategories'])->name('categories.index');
Route::get('/sitemap', [FrontendController::class, 'htmlSitemap'])->name('sitemap.page');
Route::get('/page/{page:slug}', [FrontendController::class, 'page'])->name('page.show');
Route::get('/author/{author:slug}', [FrontendController::class, 'author'])->name('author.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog:slug}', [BlogController::class, 'redirectLegacy'])->name('blog.show');
Route::get('/savings-calculator', [CalculatorController::class, 'show'])->name('calculator.show');
Route::get('/sitemap.xml', [FrontendController::class, 'sitemap'])->name('sitemap');
Route::get('/news-sitemap.xml', [FrontendController::class, 'newsSitemap'])->name('news-sitemap');
Route::get('/sitemap.txt', [FrontendController::class, 'sitemapTxt'])->name('sitemap.txt');
Route::get('/robots.txt', [FrontendController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [FrontendController::class, 'llms'])->name('llms');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('homepage', HomepageSectionController::class)->except(['show']);
        Route::post('blogs/{blog}/reset-views', [AdminBlogController::class, 'resetViews'])->name('blogs.reset-views');
        Route::resource('blogs', AdminBlogController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('authors', AuthorController::class)->except(['show']);
        Route::patch('pages/{page}/toggle-publish', [AdminPageController::class, 'togglePublish'])->name('pages.toggle-publish');
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('media', MediaLibraryController::class)->only(['index', 'store', 'destroy']);
        Route::get('advertisements/dashboard', [AdminAdvertisementController::class, 'dashboard'])->name('advertisements.dashboard');
        Route::get('advertisements/settings', [AdminAdvertisementController::class, 'settings'])->name('advertisements.settings');
        Route::post('advertisements/settings', [AdminAdvertisementController::class, 'saveSettings'])->name('advertisements.save-settings');
        Route::get('advertisements/reports', [AdminAdvertisementController::class, 'reports'])->name('advertisements.reports');
        Route::resource('advertisements', AdminAdvertisementController::class);
        Route::get('/calculator-settings', [CalculatorSettingController::class, 'edit'])->name('calculator.edit');
        Route::put('/calculator-settings', [CalculatorSettingController::class, 'update'])->name('calculator.update');
        Route::get('/branding', [BrandingController::class, 'edit'])->name('branding.edit');
        Route::put('/branding', [BrandingController::class, 'update'])->name('branding.update');
        Route::get('/footer', [FooterController::class, 'edit'])->name('footer.edit');
        Route::post('/footer/update', [FooterController::class, 'update'])->name('footer.update');
        Route::resource('navigation', NavigationController::class)->except(['show']);
        
        // SEO Manager
        Route::get('/seo', [\App\Http\Controllers\Admin\SeoManagerController::class, 'index'])->name('seo.index');
        Route::get('/seo/edit', [\App\Http\Controllers\Admin\SeoManagerController::class, 'edit'])->name('seo.edit');
        Route::post('/seo/update', [\App\Http\Controllers\Admin\SeoManagerController::class, 'update'])->name('seo.update');
        Route::post('/seo/update-robots', [\App\Http\Controllers\Admin\SeoManagerController::class, 'updateRobots'])->name('seo.update-robots');
        Route::post('/seo/clear-cache', [\App\Http\Controllers\Admin\SeoManagerController::class, 'clearCache'])->name('seo.clear-cache');

        Route::resource('redirects', AdminRedirectController::class)->except(['show']);
        Route::post('/upload-image', [ImageUploadController::class, 'store'])->name('upload.image');
        Route::post('/uploads/images', [ImageUploadController::class, 'store'])->name('images.store');
    });
});

Route::get('/{category:slug}/{blog:slug}', [BlogController::class, 'show'])->name('blog.category.show');
