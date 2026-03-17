<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrivceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         AboutUs::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'من نحن',
                'content' => 'نص تعريفي قصير عن الشركة/المشروع. يمكن أن يتضمن تاريخ المؤسسة وخدماتها الأساسية.',
                'mission' => 'تمكين العملاء من الحصول على أفضل الحلول.',
                'vision' => 'أن نصبح المرجع الأول في المجال خلال السنوات القادمة.',
                'values' => ['احترافية', 'شفافية', 'جودة', 'التزام'],
                'contact_email' => 'info@example.com',
                'contact_phone' => '+966501234567',
                'address' => 'الرياض - المملكة العربية السعودية',
                'location' => 'Riyadh, Saudi Arabia',
                'lat' => 24.7136,
                'lon' => 46.6753,
                'main_image' => 'about/main.jpg',
                'images' => [
                    'about/1.jpg',
                    'about/2.jpg',
                ],
                'website' => 'https://example.com',
                'facebook' => 'https://facebook.com/example',
                'twitter' => 'https://twitter.com/example',
                'instagram' => 'https://instagram.com/example',
                'youtube' => 'https://youtube.com/example',
                'linkedin' => 'https://linkedin.com/company/example',
            ]
        );

        TermsCondition::updateOrCreate(
            ['id' => 1],
            [
                'description' => 'الشروط والأحكام الخاصة باستخدام الموقع: باستخدامك لهذا الموقع، توافق على الامتثال للشروط التالية...'
            ]
        );


        PrivacyPolicy::updateOrCreate(
            ['id' => 1],
            [
                'description' => 'سياسة الخصوصية: نحن نحترم خصوصيتك ونتعامل مع بياناتك الشخصية وفق السياسات التالية...'
            ]
        );
    }
}
