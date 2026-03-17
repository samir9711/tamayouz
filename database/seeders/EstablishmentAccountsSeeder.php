<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EstablishmentAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // جلب كل المنشآت
        $establishments = DB::table('establishments')->get();

        $accounts = [];

        foreach ($establishments as $est) {
            // توليد slug آمن من اسم المنشأة
            $slug = Str::slug($est->name);

            // استخدام contact_email إن وجد، وإلا إنشاء بريد فريد باستخدام id لضمان التفرد
            if (!empty($est->contact_email)) {
                // إذا كان البريد الموجود يحتوي على مسافات أو أحرف كبيرة، نظفه إلى صيغة صحيحة
                $email = strtolower(trim($est->contact_email));
                // إذا كان البريد يبدو غير صالح أو مكرر، نحتاط باستخدام fall-back أدناه.
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = "{$slug}.{$est->id}@establishments.local";
                }
            } else {
                $email = "{$slug}.{$est->id}@establishments.local";
            }

            $accounts[] = [
                'establishment_id' => $est->id,
                'name' => 'Admin - ' . $est->name,
                'email' => $email,
                // كلمة المرور الافتراضية — غيّرها لاحقًا أو أرسل رابط استرجاع
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'min_admin',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($accounts)) {
            DB::table('establishment_accounts')->insert($accounts);
        }
    }
}
