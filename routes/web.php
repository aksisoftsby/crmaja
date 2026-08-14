<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientNoteController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KbArticleController;
use App\Http\Controllers\KbCategoryController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskCollaborationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('portal/login', [PortalAuthController::class, 'create'])->middleware('guest:portal')->name('portal.login');
Route::post('portal/login', [PortalAuthController::class, 'store'])->middleware('guest:portal')->name('portal.login.store');
Route::middleware('auth:portal')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [PortalAuthController::class, 'destroy'])->name('logout');
    Route::get('invoices', [ClientPortalController::class, 'invoices'])->name('invoices');
    Route::get('proposals', [ClientPortalController::class, 'proposals'])->name('proposals');
    Route::get('estimates', [ClientPortalController::class, 'estimates'])->name('estimates');
    Route::get('projects', [ClientPortalController::class, 'projects'])->name('projects');
    Route::get('tickets', [ClientPortalController::class, 'tickets'])->name('tickets');
    Route::post('tickets', [ClientPortalController::class, 'storeTicket'])->name('tickets.store');
    Route::get('knowledge-base', [ClientPortalController::class, 'knowledgeBase'])->name('knowledge-base');
    Route::get('knowledge-base/{article}', [ClientPortalController::class, 'knowledgeBaseShow'])->name('knowledge-base.show');
});

Route::middleware('auth')->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('leads', LeadController::class);
    Route::resource('items', ItemController::class)->except('show');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::resource('proposals', ProposalController::class);
    Route::resource('estimates', EstimateController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/milestones', [MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::delete('projects/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('projects.milestones.destroy');
    Route::resource('tasks', TaskController::class);
    Route::resource('tickets', TicketController::class);
    Route::resource('kb-articles', KbArticleController::class);
    Route::post('kb-categories', [KbCategoryController::class, 'store'])->name('kb-categories.store');
    Route::delete('kb-categories/{category}', [KbCategoryController::class, 'destroy'])->name('kb-categories.destroy');
    Route::post('tickets/{ticket}/replies', [TicketReplyController::class, 'store'])->name('tickets.replies.store');
    Route::post('tasks/{task}/checklist-items', [TaskCollaborationController::class, 'storeChecklist'])->name('tasks.checklist-items.store');
    Route::patch('tasks/{task}/checklist-items/{checklistItem}/toggle', [TaskCollaborationController::class, 'toggleChecklist'])->name('tasks.checklist-items.toggle');
    Route::delete('tasks/{task}/checklist-items/{checklistItem}', [TaskCollaborationController::class, 'destroyChecklist'])->name('tasks.checklist-items.destroy');
    Route::post('tasks/{task}/comments', [TaskCollaborationController::class, 'storeComment'])->name('tasks.comments.store');
    Route::delete('tasks/{task}/comments/{comment}', [TaskCollaborationController::class, 'destroyComment'])->name('tasks.comments.destroy');
    Route::post('tasks/{task}/time-logs', [TaskCollaborationController::class, 'storeTimeLog'])->name('tasks.time-logs.store');
    Route::patch('tasks/{task}/time-logs/{timeLog}/stop', [TaskCollaborationController::class, 'stopTimeLog'])->name('tasks.time-logs.stop');
    Route::delete('tasks/{task}/time-logs/{timeLog}', [TaskCollaborationController::class, 'destroyTimeLog'])->name('tasks.time-logs.destroy');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::delete('invoices/{invoice}/payments/{payment}', [PaymentController::class, 'destroy'])->name('invoices.payments.destroy');
    Route::post('clients/{client}/contacts', [ContactController::class, 'store'])->name('clients.contacts.store');
    Route::post('clients/{client}/notes', [ClientNoteController::class, 'store'])->name('clients.notes.store');
    Route::delete('clients/{client}/notes/{note}', [ClientNoteController::class, 'destroy'])->name('clients.notes.destroy');
    Route::put('clients/{client}/contacts/{contact}', [ContactController::class, 'update'])->name('clients.contacts.update');
    Route::delete('clients/{client}/contacts/{contact}', [ContactController::class, 'destroy'])->name('clients.contacts.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
