<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AccessControlSeeder::class);
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();

        $client = Client::firstOrCreate(
            ['company_name' => 'PT Nusantara Digital'],
            ['phone' => '021-555-0101', 'city' => 'Jakarta', 'country' => 'Indonesia', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true],
        );
        $contact = Contact::withTrashed()->firstOrCreate(
            ['email' => 'client@aksisoft.test'],
            ['client_id' => $client->id, 'first_name' => 'Budi', 'last_name' => 'Santoso', 'phone' => '0812-0000-0101', 'title' => 'Director', 'is_primary' => true, 'is_active' => true, 'password' => Hash::make('ClientPass123!')],
        );
        if ($contact->trashed()) {
            $contact->restore();
        }

        $item = Item::firstOrCreate(['title' => 'Implementasi CRM'], ['description' => 'Implementasi dan konfigurasi Aksisoft CRM', 'rate' => 15000000, 'unit' => 'paket', 'is_active' => true]);
        $source = LeadSource::query()->firstOrFail();
        $status = LeadStatus::query()->firstOrFail();
        Lead::firstOrCreate(['email' => 'prospek@contoh.test'], ['name' => 'Andi Prospek', 'company_name' => 'CV Maju Bersama', 'phone' => '0812-0000-0102', 'source_id' => $source->id, 'status_id' => $status->id, 'assigned_to' => $admin->id, 'lead_value' => 25000000]);

        $project = Project::firstOrCreate(['name' => 'Transformasi CRM Nusantara'], ['client_id' => $client->id, 'description' => 'Implementasi CRM, migrasi data, dan pelatihan pengguna.', 'start_date' => now()->startOfMonth(), 'deadline' => now()->addMonth(), 'status' => 'in_progress', 'billing_type' => 'fixed', 'budget' => 15000000, 'progress' => 50, 'created_by' => $admin->id]);
        $project->members()->syncWithoutDetaching([$admin->id]);
        Task::firstOrCreate(['name' => 'Konfigurasi modul sales'], ['priority' => 'high', 'status' => 'in_progress', 'related_type' => Project::class, 'related_id' => $project->id, 'created_by' => $admin->id])->assignees()->syncWithoutDetaching([$admin->id]);

        $invoice = Invoice::firstOrCreate(['number' => 'INV-DEMO-2026-001'], ['client_id' => $client->id, 'date' => now()->startOfMonth(), 'due_date' => now()->addDays(14), 'status' => 'partial', 'subtotal' => 15000000, 'discount' => 0, 'total' => 15000000, 'paid_amount' => 5000000, 'notes' => 'Invoice demo implementasi CRM.']);
        $invoice->items()->firstOrCreate(['description' => $item->title], ['item_id' => $item->id, 'qty' => 1, 'rate' => 15000000, 'amount' => 15000000]);

        $department = TicketDepartment::query()->firstOrFail();
        $ticket = Ticket::firstOrCreate(['number' => 'TKT-DEMO-2026-001'], ['subject' => 'Pertanyaan konfigurasi dashboard', 'client_id' => $client->id, 'contact_id' => $contact->id, 'department_id' => $department->id, 'priority' => 'medium', 'status' => 'open', 'assigned_to' => $admin->id, 'source' => 'portal']);
        $ticket->replies()->firstOrCreate(['message' => 'Mohon panduan untuk menyesuaikan dashboard.', 'user_type' => 'contact', 'user_id' => $contact->id], ['is_internal_note' => false]);

        $category = KbCategory::firstOrCreate(['name' => 'Panduan Memulai']);
        KbArticle::firstOrCreate(['slug' => 'memulai-dengan-aksisoft-crm'], ['category_id' => $category->id, 'title' => 'Memulai dengan Aksisoft CRM', 'content' => 'Buat pelanggan, kelola lead, lalu siapkan dokumen penjualan dari menu yang tersedia.', 'is_published' => true, 'is_client_only' => false, 'created_by' => $admin->id]);
    }
}
