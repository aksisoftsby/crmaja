<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProvisionAdminCommand extends Command
{
    protected $signature = 'crm:provision-admin
        {email : Alamat email administrator}
        {--name= : Nama administrator}
        {--password= : Password administrator; omit to input securely}';

    protected $description = 'Provision a Super Admin explicitly without relying on a default production credential';

    public function handle(): int
    {
        $email = strtolower((string) $this->argument('email'));
        $name = (string) ($this->option('name') ?: $this->ask('Nama administrator', 'Aksisoft Super Admin'));
        $password = (string) ($this->option('password') ?: $this->secret('Password administrator'));

        if (mb_strlen($password) < 12) {
            $this->error('Password administrator harus memiliki sedikitnya 12 karakter.');

            return self::FAILURE;
        }

        $role = Role::findByName('Super Admin', 'web');
        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $name,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
        $user->save();
        $user->syncRoles([$role]);

        $this->info("Super Admin {$user->email} berhasil diprovision.");

        return self::SUCCESS;
    }
}
