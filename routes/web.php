<?php

use App\Http\Controllers\AttractionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

/*
| Setiap halaman publik didaftarkan dua kali:
|   Bahasa Indonesia tanpa prefix  → nama route "id.*"  (/paket)
|   English dengan prefix /en      → nama route "en.*"  (/en/packages)
| Gunakan helper lroute('packages.index') agar otomatis mengikuti bahasa aktif.
*/
foreach (['id' => '', 'en' => 'en'] as $locale => $prefix) {
    $p = config("sukajadi.paths.$locale");

    Route::prefix($prefix)
        ->name("$locale.")
        ->middleware("locale:$locale")
        ->group(function () use ($p) {
            Route::get('/', [PageController::class, 'home'])->name('home');
            Route::get($p['profile'], [PageController::class, 'profile'])->name('profile');
            Route::get($p['about'], [PageController::class, 'about'])->name('about');
            Route::get($p['faq'], [PageController::class, 'faq'])->name('faq');
            Route::get($p['contact'], [PageController::class, 'contact'])->name('contact');
            Route::post($p['contact'], [PageController::class, 'sendMessage'])->middleware('throttle:5,1')->name('contact.send');
            Route::get($p['reviews'], [PageController::class, 'reviews'])->name('reviews');
            Route::post($p['reviews'], [PageController::class, 'storeReview'])->middleware('throttle:5,1')->name('reviews.store');

            Route::get($p['packages'], [PackageController::class, 'index'])->name('packages.index');
            Route::get($p['packages'].'/{slug}', [PackageController::class, 'show'])->name('packages.show');
            Route::get($p['custom'], [PackageController::class, 'custom'])->name('custom');

            Route::get($p['attractions'], [AttractionController::class, 'index'])->name('attractions.index');
            Route::get($p['attractions'].'/{slug}', [AttractionController::class, 'show'])->name('attractions.show');

            Route::get($p['umkm'], [UmkmController::class, 'index'])->name('umkm.index');
            Route::get($p['umkm'].'/{slug}', [UmkmController::class, 'show'])->name('umkm.show');
            Route::get($p['products'], [UmkmController::class, 'products'])->name('products');

            Route::get($p['partners'], [PartnerController::class, 'index'])->name('partners.index');
            Route::get($p['partners'].'/{slug}', [PartnerController::class, 'show'])->name('partners.show');
            Route::get($p['team'], [PageController::class, 'team'])->name('team');

            Route::get($p['gallery'], [GalleryController::class, 'index'])->name('gallery.index');
            Route::get($p['gallery'].'/{slug}', [GalleryController::class, 'show'])->name('gallery.show');

            Route::get($p['news'], [NewsController::class, 'index'])->name('news.index');
            Route::get($p['news'].'/{slug}', [NewsController::class, 'show'])->name('news.show');

            Route::get($p['booking'], [BookingController::class, 'create'])->name('booking.create');
            Route::post($p['booking'], [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
            Route::get($p['booking'].'/'.$p['booking_done'].'/{booking:code}', [BookingController::class, 'done'])->middleware('signed')->name('booking.done');
            Route::get($p['booking_check'], [BookingController::class, 'check'])->middleware('throttle:20,1')->name('booking.check');
        });
}

Route::get('booking/availability', [BookingController::class, 'availability'])->middleware('throttle:60,1')->name('booking.availability');
Route::get('wa', WhatsappController::class)->middleware('throttle:60,1')->name('wa');
Route::get('placeholder.svg', PlaceholderController::class)->name('placeholder');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
