<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MinistryAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();


        $ministries = DB::table('ministries')->get();

        $accounts = [];

        foreach ($ministries as $min) {

            $slug = Str::slug($min->name ?: 'ministry');


            $email = null;
            if (!empty($min->contact_email)) {
                $candidate = strtolower(trim($min->contact_email));
                if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {

                    $exists = DB::table('ministry_accounts')->where('email', $candidate)->exists();
                    if (! $exists) {
                        $email = $candidate;
                    }
                }
            }

            if (is_null($email)) {
                $email = "{$slug}.{$min->id}@ministries.local";
            }


            $baseEmail = $email;
            $suffix = 1;
            while (DB::table('ministry_accounts')->where('email', $email)->exists()) {
                $email = preg_replace('/(^[^@]+)(@.+$)/', '$1', $baseEmail) . "-{$suffix}" . strstr($baseEmail, '@');
                $suffix++;
            }

            $accounts[] = [
                'ministry_id' => $min->id,
                'name' => 'Admin - ' . ($min->name ?? 'Ministry ' . $min->id),
                'email' => $email,
               
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'min_admin',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($accounts)) {
            DB::table('ministry_accounts')->insert($accounts);
        }
    }
}
