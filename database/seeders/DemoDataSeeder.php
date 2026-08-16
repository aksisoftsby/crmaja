<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\CustomerGroup;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Milestone;
use App\Models\Note;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\TaskComment;
use App\Models\TaskTimeLog;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private const RECORD_COUNT = 24;

    public function run(): void
    {
        $this->call(AccessControlSeeder::class);

        // Explicit demo seeding intentionally provisions demo-only credentials.
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@aksisoft.test'],
            [
                'name' => 'Aksisoft Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('ChangeMe123!'),
            ],
        );
        $admin->syncRoles(['Super Admin']);
        $staff = $this->seedStaff();
        $this->seedRoleDemoAccounts();
        $groups = $this->seedCustomerGroups();
        $sources = $this->seedLeadSources();
        $statuses = $this->seedLeadStatuses();
        $departments = $this->seedTicketDepartments();
        $categories = $this->seedKnowledgeBaseCategories();
        $items = $this->seedItems();

        foreach (range(1, self::RECORD_COUNT) as $index) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $assignedStaff = $staff[($index - 1) % count($staff)];
            $group = $groups[($index - 1) % count($groups)];
            $source = $sources[($index - 1) % count($sources)];
            $status = $statuses[($index - 1) % count($statuses)];
            $department = $departments[($index - 1) % count($departments)];
            $category = $categories[($index - 1) % count($categories)];
            $item = $items[($index - 1) % count($items)];
            $company = $this->companyName($index);
            $contactEmail = $index === 1 ? 'client@aksisoft.test' : "contact{$number}@demo.aksisoft.test";
            $rate = 3500000 + ($index * 275000);
            $quantity = ($index % 3) + 1;
            $subtotal = $rate * $quantity;
            $discount = $index % 4 === 0 ? 250000 : 0;
            $total = $subtotal - $discount;
            $invoiceStatus = $index % 4 === 0 ? 'paid' : 'partial';
            $paidAmount = $invoiceStatus === 'paid' ? $total : $total / 2;

            $client = Client::query()->updateOrCreate(
                ['company_name' => $company],
                [
                    'vat_number' => "ID-PPN-2026-{$number}",
                    'phone' => sprintf('+62 21 7000 %04d', $index),
                    'website' => "https://{$this->slug($company)}.demo.aksisoft.test",
                    'address' => "Jl. Inovasi Bisnis No. {$index}, Gedung Aksi Lantai ".(($index % 12) + 1),
                    'city' => $this->city($index),
                    'state' => $this->province($index),
                    'zip' => sprintf('%05d', 10000 + $index),
                    'country' => 'Indonesia',
                    'currency' => 'IDR',
                    'customer_group_id' => $group->id,
                    'assigned_staff_id' => $assignedStaff->id,
                    'is_active' => true,
                    'created_by' => $admin->id,
                ],
            );

            $contact = Contact::withTrashed()->firstOrNew(['email' => $contactEmail]);
            $contact->fill([
                'client_id' => $client->id,
                'first_name' => $this->firstName($index),
                'last_name' => $this->lastName($index),
                'phone' => sprintf('+62 812 7000 %04d', $index),
                'title' => $this->jobTitle($index),
                'is_primary' => true,
                'is_active' => true,
            ]);
            if (! $contact->exists) {
                $contact->password = Hash::make($index === 1 ? 'ClientPass123!' : 'DemoClientPass123!');
            }
            $contact->save();
            if ($contact->trashed()) {
                $contact->restore();
            }

            $lead = Lead::query()->updateOrCreate(
                ['email' => "lead{$number}@demo.aksisoft.test"],
                [
                    'name' => "{$this->firstName($index)} {$this->lastName($index)}",
                    'company_name' => $company,
                    'phone' => sprintf('+62 813 8000 %04d', $index),
                    'source_id' => $source->id,
                    'status_id' => $status->id,
                    'assigned_to' => $assignedStaff->id,
                    'lead_value' => $total * 1.25,
                    'notes' => "Lead demo {$number} untuk kebutuhan CRM {$this->industry($index)}. Jadwal follow-up disiapkan oleh tim sales.",
                    'is_converted' => $index % 3 === 0,
                    'converted_client_id' => $index % 3 === 0 ? $client->id : null,
                    'converted_at' => $index % 3 === 0 ? now()->subDays($index) : null,
                    'created_by' => $admin->id,
                ],
            );

            $proposal = Proposal::query()->updateOrCreate(
                ['number' => "PRP-DEMO-2026-{$number}"],
                [
                    'client_id' => $client->id,
                    'lead_id' => $lead->id,
                    'subject' => "Proposal Implementasi Aksi CRM untuk {$company}",
                    'content' => "Proposal demo untuk digitalisasi sales, layanan pelanggan, dan operasi proyek {$company}. Ruang lingkup mencakup implementasi, pelatihan, serta pendampingan awal.",
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'status' => ['draft', 'sent', 'open', 'accepted'][$index % 4],
                    'date' => now()->subDays(50 - $index)->toDateString(),
                    'open_till' => now()->addDays(14 + $index)->toDateString(),
                ],
            );
            $proposal->items()->updateOrCreate(
                ['description' => "{$item->title} — Paket Proposal {$number}"],
                ['item_id' => $item->id, 'qty' => $quantity, 'rate' => $rate, 'amount' => $subtotal],
            );

            $estimate = Estimate::query()->updateOrCreate(
                ['number' => "EST-DEMO-2026-{$number}"],
                [
                    'client_id' => $client->id,
                    'date' => now()->subDays(45 - $index)->toDateString(),
                    'expiry_date' => now()->addDays(21 + $index)->toDateString(),
                    'status' => ['draft', 'sent', 'accepted', 'declined'][$index % 4],
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => "Estimasi demo {$number} untuk {$company}; valid selama 30 hari.",
                ],
            );
            $estimate->items()->updateOrCreate(
                ['description' => "{$item->title} — Estimasi {$number}"],
                ['item_id' => $item->id, 'qty' => $quantity, 'rate' => $rate, 'amount' => $subtotal],
            );

            $invoice = Invoice::query()->updateOrCreate(
                ['number' => "INV-DEMO-2026-{$number}"],
                [
                    'client_id' => $client->id,
                    'date' => now()->subDays(30 - $index)->toDateString(),
                    'due_date' => now()->addDays($index % 2 === 0 ? 14 : -$index)->toDateString(),
                    'status' => $invoiceStatus,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'notes' => "Invoice demo {$number} untuk layanan {$item->title} pada {$company}.",
                ],
            );
            $invoice->items()->updateOrCreate(
                ['description' => "{$item->title} — Invoice {$number}"],
                ['item_id' => $item->id, 'qty' => $quantity, 'rate' => $rate, 'amount' => $subtotal],
            );
            $payment = Payment::query()->firstOrNew(['transaction_id' => "PAY-DEMO-2026-{$number}"]);
            $payment->invoice_id = $invoice->id;
            $payment->recorded_by = $assignedStaff->id;
            $payment->fill([
                'amount' => $paidAmount,
                'paid_at' => now()->subDays($index)->toDateString(),
                'payment_mode' => ['transfer', 'cash', 'credit_card', 'virtual_account'][$index % 4],
                'transaction_id' => "PAY-DEMO-2026-{$number}",
                'note' => "Pembayaran demo {$number} dari {$company}.",
            ]);
            $payment->save();

            $project = Project::query()->updateOrCreate(
                ['name' => "Implementasi Aksi CRM — {$company}"],
                [
                    'client_id' => $client->id,
                    'description' => "Proyek demo implementasi Aksi CRM untuk {$company}, termasuk konfigurasi workflow, migrasi data, dan pelatihan tim.",
                    'start_date' => now()->subDays(20 + $index)->toDateString(),
                    'deadline' => now()->addDays(30 + $index)->toDateString(),
                    'status' => ['planning', 'in_progress', 'on_hold', 'completed'][$index % 4],
                    'billing_type' => ['fixed', 'project_hours', 'task_hours'][$index % 3],
                    'budget' => $total * 2,
                    'progress' => min(100, 20 + ($index * 3)),
                    'created_by' => $admin->id,
                ],
            );
            $project->members()->syncWithoutDetaching([$admin->id, $assignedStaff->id]);

            $milestone = Milestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => "Milestone {$number}: Go-Live"],
                [
                    'due_date' => now()->addDays(14 + $index)->toDateString(),
                    'description' => "Target go-live dan serah terima fase utama untuk {$company}.",
                    'sort_order' => $index,
                ],
            );

            $task = Task::query()->updateOrCreate(
                ['name' => "Konfigurasi workflow {$company}"],
                [
                    'description' => "Menyiapkan pipeline sales, tiket, dokumen penjualan, dan dashboard untuk {$company}.",
                    'priority' => ['low', 'medium', 'high', 'urgent'][$index % 4],
                    'status' => ['not_started', 'in_progress', 'testing', 'completed'][$index % 4],
                    'start_date' => now()->subDays($index)->toDateString(),
                    'due_date' => now()->addDays(7 + $index)->toDateString(),
                    'milestone_id' => $milestone->id,
                    'related_type' => Project::class,
                    'related_id' => $project->id,
                    'is_recurring' => $index % 6 === 0,
                    'created_by' => $admin->id,
                ],
            );
            $task->assignees()->syncWithoutDetaching([$assignedStaff->id]);
            TaskChecklistItem::query()->updateOrCreate(
                ['task_id' => $task->id, 'description' => "Validasi konfigurasi workflow {$number}"],
                ['is_finished' => $index % 4 === 0, 'sort_order' => 1],
            );
            TaskComment::query()->updateOrCreate(
                ['task_id' => $task->id, 'user_id' => $assignedStaff->id, 'comment' => "Catatan progres demo {$number}: konfigurasi sedang ditinjau bersama pengguna utama."],
                [],
            );
            TaskTimeLog::query()->updateOrCreate(
                ['task_id' => $task->id, 'note' => "Pencatatan waktu implementasi demo {$number}"],
                [
                    'user_id' => $assignedStaff->id,
                    'start_time' => now()->subDays($index)->setTime(9, 0),
                    'end_time' => now()->subDays($index)->setTime(11, 30),
                ],
            );

            $ticket = Ticket::query()->updateOrCreate(
                ['number' => "TKT-DEMO-2026-{$number}"],
                [
                    'subject' => "Permintaan bantuan konfigurasi {$company}",
                    'client_id' => $client->id,
                    'contact_id' => $contact->id,
                    'department_id' => $department->id,
                    'priority' => ['low', 'medium', 'high', 'urgent'][$index % 4],
                    'status' => ['open', 'in_progress', 'answered', 'closed'][$index % 4],
                    'assigned_to' => $assignedStaff->id,
                    'source' => $index % 2 === 0 ? 'portal' : 'email',
                    'created_by' => $admin->id,
                ],
            );
            TicketReply::query()->updateOrCreate(
                ['ticket_id' => $ticket->id, 'message' => "Balasan demo {$number}: tim Aksi CRM sedang menyiapkan panduan konfigurasi untuk {$company}."],
                ['user_type' => 'staff', 'user_id' => $assignedStaff->id, 'is_internal_note' => $index % 5 === 0],
            );

            $article = KbArticle::query()->updateOrCreate(
                ['slug' => "panduan-demo-aksi-crm-{$number}"],
                [
                    'category_id' => $category->id,
                    'title' => "Panduan Aksi CRM {$number}: {$this->industry($index)}",
                    'content' => "Artikel demo {$number} berisi panduan operasional CRM untuk kebutuhan {$this->industry($index)}. Artikel mencakup pengelolaan pelanggan, lead, dokumen, proyek, dan tiket.",
                    'is_published' => true,
                    'is_client_only' => $index % 3 === 0,
                    'views_count' => 10 + ($index * 7),
                    'created_by' => $admin->id,
                ],
            );

            Note::query()->updateOrCreate(
                ['related_type' => Client::class, 'related_id' => $client->id, 'content' => "Catatan internal demo {$number} untuk {$company}: peluang ekspansi modul CRM pada kuartal berikutnya."],
                ['created_by' => $assignedStaff->id],
            );
        }
    }

    /** @return array<int, User> */
    private function seedStaff(): array
    {
        $staff = [];
        foreach (range(1, self::RECORD_COUNT) as $index) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $user = User::query()->firstOrCreate(
                ['email' => "staff{$number}@demo.aksisoft.test"],
                [
                    'name' => "{$this->firstName($index)} {$this->lastName($index)}",
                    'email_verified_at' => now(),
                    'password' => Hash::make('DemoStaffPass123!'),
                    'remember_token' => Str::random(10),
                ],
            );
            $user->syncRoles(['Staff']);
            $staff[] = $user;
        }

        return $staff;
    }

    private function seedRoleDemoAccounts(): void
    {
        foreach ([
            ['name' => 'Demo Sales Aksi CRM', 'email' => 'sales@demo.aksisoft.test', 'role' => 'Sales'],
            ['name' => 'Demo Support Aksi CRM', 'email' => 'support@demo.aksisoft.test', 'role' => 'Support'],
        ] as $account) {
            $user = User::query()->firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('DemoRolePass123!'),
                    'remember_token' => Str::random(10),
                ],
            );
            $user->syncRoles([$account['role']]);
        }
    }

    /** @return array<int, CustomerGroup> */
    private function seedCustomerGroups(): array
    {
        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => CustomerGroup::query()->updateOrCreate(
                ['name' => 'Segmen Demo '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                [],
            ))
            ->all();
    }

    /** @return array<int, LeadSource> */
    private function seedLeadSources(): array
    {
        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => LeadSource::query()->updateOrCreate(
                ['name' => 'Sumber Demo '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                [],
            ))
            ->all();
    }

    /** @return array<int, LeadStatus> */
    private function seedLeadStatuses(): array
    {
        $colors = ['#0F766E', '#0284C7', '#7C3AED', '#B45309', '#BE123C', '#166534'];

        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => LeadStatus::query()->updateOrCreate(
                ['name' => 'Tahap Demo '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                ['color' => $colors[($index - 1) % count($colors)], 'sort_order' => $index + 100, 'is_default' => false],
            ))
            ->all();
    }

    /** @return array<int, TicketDepartment> */
    private function seedTicketDepartments(): array
    {
        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => TicketDepartment::query()->updateOrCreate(
                ['name' => 'Departemen Demo '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                ['email_piping_address' => 'support+'.str_pad((string) $index, 2, '0', STR_PAD_LEFT).'@demo.aksisoft.test'],
            ))
            ->all();
    }

    /** @return array<int, KbCategory> */
    private function seedKnowledgeBaseCategories(): array
    {
        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => KbCategory::query()->updateOrCreate(
                ['name' => 'Kategori Demo '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                ['parent_id' => null],
            ))
            ->all();
    }

    /** @return array<int, Item> */
    private function seedItems(): array
    {
        return collect(range(1, self::RECORD_COUNT))
            ->map(fn (int $index) => Item::query()->updateOrCreate(
                ['title' => 'Paket Aksi CRM '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                [
                    'description' => "Layanan demo Aksi CRM {$index}: implementasi, konsultasi, dan dukungan bisnis.",
                    'rate' => 3500000 + ($index * 275000),
                    'unit' => $index % 2 === 0 ? 'paket' : 'jam',
                    'is_active' => true,
                ],
            ))
            ->all();
    }

    private function companyName(int $index): string
    {
        $prefixes = ['PT', 'CV', 'PT', 'PT', 'CV', 'PT'];
        $brands = ['Astra Solusi', 'Nusantara Prima', 'Kreasi Digital', 'Mitra Inovasi', 'Cakrawala Teknologi', 'Sentosa Niaga', 'Berkah Utama', 'Lintas Data'];

        return $prefixes[($index - 1) % count($prefixes)].' '.$brands[($index - 1) % count($brands)].' '.$index;
    }

    private function firstName(int $index): string
    {
        return ['Aditya', 'Bima', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hendra', 'Intan', 'Joko', 'Kartika', 'Lukman'][($index - 1) % 12];
    }

    private function lastName(int $index): string
    {
        return ['Pratama', 'Santoso', 'Wijaya', 'Kusuma', 'Saputra', 'Maharani', 'Nugroho', 'Permata', 'Hidayat', 'Lestari', 'Ramadhan', 'Putri'][($index - 1) % 12];
    }

    private function jobTitle(int $index): string
    {
        return ['Direktur Utama', 'Head of Sales', 'Operations Manager', 'Finance Manager', 'Customer Success Lead', 'IT Manager'][($index - 1) % 6];
    }

    private function industry(int $index): string
    {
        return ['distribusi', 'manufaktur', 'layanan profesional', 'retail', 'logistik', 'teknologi', 'pendidikan', 'kesehatan'][($index - 1) % 8];
    }

    private function city(int $index): string
    {
        return ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan', 'Makassar', 'Denpasar'][($index - 1) % 8];
    }

    private function province(int $index): string
    {
        return ['DKI Jakarta', 'Jawa Barat', 'Jawa Timur', 'Jawa Tengah', 'DI Yogyakarta', 'Sumatera Utara', 'Sulawesi Selatan', 'Bali'][($index - 1) % 8];
    }

    private function slug(string $value): string
    {
        return Str::slug($value);
    }
}
