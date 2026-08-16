<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    /**
     * Seed the initial CRM access-control configuration.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'profile.manage',
            'staff.view_all',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'roles.manage',
            'clients.view_own',
            'clients.view_all',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'leads.view_own',
            'leads.view_all',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'items.view',
            'items.create',
            'items.edit',
            'items.delete',
            'proposals.view',
            'proposals.create',
            'proposals.edit',
            'proposals.delete',
            'estimates.view',
            'estimates.create',
            'estimates.edit',
            'estimates.delete',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'projects.view_own',
            'projects.view_all',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'tasks.view_own',
            'tasks.view_all',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'tickets.view_own',
            'tickets.view_all',
            'tickets.create',
            'tickets.edit',
            'tickets.delete',
            'knowledge_base.view',
            'knowledge_base.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $staff = Role::findOrCreate('Staff', 'web');
        $sales = Role::findOrCreate('Sales', 'web');
        $support = Role::findOrCreate('Support', 'web');

        $superAdmin->syncPermissions($permissions);
        $staff->syncPermissions(['dashboard.view', 'profile.manage', 'clients.view_own', 'leads.view_own', 'projects.view_own', 'tasks.view_own', 'tasks.create', 'tickets.view_own', 'tickets.create', 'knowledge_base.view']);
        $sales->syncPermissions(['dashboard.view', 'profile.manage', 'clients.view_all', 'clients.create', 'clients.edit', 'leads.view_all', 'leads.create', 'leads.edit', 'items.view', 'proposals.view', 'proposals.create', 'proposals.edit', 'estimates.view', 'estimates.create', 'estimates.edit', 'invoices.view', 'invoices.create', 'invoices.edit', 'projects.view_all', 'projects.create', 'projects.edit', 'tasks.view_all', 'tasks.create', 'tasks.edit', 'reports.view']);
        $support->syncPermissions(['dashboard.view', 'profile.manage', 'clients.view_all', 'projects.view_own', 'tasks.view_own', 'tasks.create', 'tasks.edit', 'tickets.view_all', 'tickets.create', 'tickets.edit', 'knowledge_base.view']);

        foreach (['Dukungan Umum', 'Teknis', 'Penagihan'] as $departmentName) {
            TicketDepartment::firstOrCreate(['name' => $departmentName]);
        }

        foreach (['Website', 'Referral', 'WhatsApp', 'Email', 'Pameran'] as $sourceName) {
            LeadSource::firstOrCreate(['name' => $sourceName]);
        }

        foreach ([
            ['name' => 'Baru', 'color' => '#6366F1', 'sort_order' => 1, 'is_default' => true],
            ['name' => 'Dihubungi', 'color' => '#0EA5E9', 'sort_order' => 2, 'is_default' => false],
            ['name' => 'Kualifikasi', 'color' => '#F59E0B', 'sort_order' => 3, 'is_default' => false],
            ['name' => 'Menang', 'color' => '#10B981', 'sort_order' => 4, 'is_default' => false],
            ['name' => 'Kalah', 'color' => '#EF4444', 'sort_order' => 5, 'is_default' => false],
        ] as $status) {
            LeadStatus::updateOrCreate(['name' => $status['name']], $status);
        }

        // A deterministic account is safe only for isolated test and local environments.
        // Production administrators must be provisioned explicitly via crm:provision-admin.
        if (app()->environment('local') || app()->runningUnitTests()) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@aksisoft.test'],
                [
                    'name' => 'Aksisoft Super Admin',
                    'password' => Hash::make('ChangeMe123!'),
                    'email_verified_at' => now(),
                ],
            );

            $admin->syncRoles([$superAdmin]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
