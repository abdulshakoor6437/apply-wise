<?php

use App\Livewire\Applications\Index as ApplicationsIndex;
use App\Livewire\Companies\Index as CompaniesIndex;
use App\Livewire\Contacts\Index as ContactsIndex;
use App\Livewire\Documents\Index as DocumentsIndex;
use Illuminate\Support\Facades\Route;

// Root — redirect to dashboard or welcome
Route::view('/', 'welcome');

// Auth-protected routes — all behind auth + verified middleware
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('dashboard', App\Livewire\Dashboard::class)->name('dashboard');

    // Applications / Kanban board
    Route::get('applications', ApplicationsIndex::class)->name('applications');
    Route::get('applications/{application}', App\Livewire\Applications\Show::class)->name('applications.show');

    // Companies
    Route::get('companies', CompaniesIndex::class)->name('companies');

    // Contacts
    Route::get('contacts', ContactsIndex::class)->name('contacts');

    // Documents
    Route::get('documents', DocumentsIndex::class)->name('documents');
    Route::get('documents/{document}/download', App\Http\Controllers\DocumentDownloadController::class)->name('documents.download');

    // Notifications
    Route::get('notifications', App\Livewire\Notifications\Index::class)->name('notifications.index');
});

// Profile (auth only, no verified required)
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
