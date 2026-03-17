<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DirectoratesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $insertMany = [];

        // Utility: fetch ministry by (partial) Arabic name
        $getMinistryId = function(string $nameLike) {
            $m = DB::table('ministries')->where('name', 'like', $nameLike . '%')->first();
            return $m ? $m->id : null;
        };

        /*
         * 1) Ministry of Health - مديريات موثقة من هيكل الوزارة الرسمي (قوائم داخلية).
         * Source: وزارة الصحة / ويكيبيديا (انظر المراجع أسفل السكريدر).
         */
        $mid = $getMinistryId('وزارة الصحة');
        if ($mid) {
            $mohDirectorates = [
                ['name' => 'مديرية المستشفيات', 'description' => 'إدارة وتشغيل وصيانة المستشفيات العامة والخاصة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الطوارئ والإسعاف', 'description' => 'إدارة خدمات الإسعاف والطوارئ الوطنية.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الصيدلة وشؤون الدواء', 'description' => 'تنظيم أدوية، تراخيص وصيدلة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية المختبرات العامة والصحة العامة', 'description' => 'إشراف على مختبرات الصحة العامة ومراقبة الأمراض.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية المهن الصحية', 'description' => 'شؤون تراخيص الكوادر الصحية والتسجيل.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية التخطيط والدراسات الاستراتيجية', 'description' => 'التخطيط الصحي وجمع البيانات والمؤشرات.', 'contact_phone' => null, 'contact_email' => null],
            ];

            foreach ($mohDirectorates as $d) {
                $insertMany[] = array_merge($d, [
                    'ministry_id' => $mid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        /*
         * 2) Ministry of Higher Education and Scientific Research
         * (قائمة مديريات منشورة في هيكل الوزارة الرسمي / صفحة الوزارة).
         */
        $mid = $getMinistryId('وزارة التعليم العالي');
        if ($mid) {
            $moheDirectorates = [
                ['name' => 'مديرية شؤون الطلبة', 'description' => 'قضايا القبول والمنح والتحويلات والبعثات.', 'contact_phone' => '00963112119865', 'contact_email' => 'mhe@mhe.gov.sy'],
                ['name' => 'مديرية البحث العلمي', 'description' => 'تنسيق الأنشطة البحثية ودعم البحوث العلمية.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية العلاقات الثقافية', 'description' => 'التعاون الدولي والعلاقات الثقافية الأكاديمية.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية المعادلة واعتراف الشهادات', 'description' => 'معادلة الشهادات وتقييمها.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الجودة والاعتماد', 'description' => 'معايير الجودة والاعتماد في مؤسسات التعليم العالي.', 'contact_phone' => null, 'contact_email' => null],
            ];

            foreach ($moheDirectorates as $d) {
                $insertMany[] = array_merge($d, [
                    'ministry_id' => $mid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        /*
         * 3) Ministry of Education (التربية) - أمثلة مديريات مركزية وإدارية شائعة
         * (موجودة بالمواقع الرسمية مثل مديرية الامتحانات وما ينشره موقع الوزارة).
         */
        $mid = $getMinistryId('وزارة التربية') ?: $getMinistryId('وزارة التعليم');
        if ($mid) {
            $moeDirectorates = [
                ['name' => 'مديرية الامتحانات', 'description' => 'تنظيم الامتحانات العامة والتابعة للوزارة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية المناهج والتوجيه', 'description' => 'إعداد المناهج الدراسية والمراجع التعليمية.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية التعليم الأساسي والثانوي', 'description' => 'إدارة شؤون المدارس والالتزام التعليمي.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية التعليم الخاص', 'description' => 'إشراف وتراخيص المؤسسات التعليمية الخاصة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية التخطيط والإحصاء', 'description' => 'جمع وتحليل بيانات القطاع التعليمي.', 'contact_phone' => null, 'contact_email' => null],
            ];

            foreach ($moeDirectorates as $d) {
                $insertMany[] = array_merge($d, [
                    'ministry_id' => $mid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        /*
         * 4) Ministry of Agriculture and Agrarian Reform
         * (مديريات زراعية شائعة: الانتاج النباتي، الحيواني، الوقاية النباتية، التخطيط).
         */
        $mid = $getMinistryId('وزارة الزراعة');
        if ($mid) {
            $agriDirectorates = [
                ['name' => 'مديرية الانتاج النباتي', 'description' => 'شؤون المحاصيل والبذور والري.' , 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الانتاج الحيواني', 'description' => 'شؤون الثروة الحيوانية والبيطري.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الوقاية النباتية', 'description' => 'مكافحة الآفات والأمراض النباتية.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية التخطيط والبحوث الزراعية', 'description' => 'التخطيط الزراعي والدراسات والبحوث.', 'contact_phone' => null, 'contact_email' => null],
            ];

            foreach ($agriDirectorates as $d) {
                $insertMany[] = array_merge($d, [
                    'ministry_id' => $mid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        /*
         * 5) Ministry of Interior - مديريات أمنية وإدارية (قائمة وظائفية شائعة).
         */
        $mid = $getMinistryId('وزارة الداخلية');
        if ($mid) {
            $interiorDirectorates = [
                ['name' => 'المديرية العامة للأمن الداخلي', 'description' => 'شؤون الأمن الداخلي والشرطة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية الجوازات والهجرة', 'description' => 'شؤون الجوازات والهجرة والإقامة.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية المرور', 'description' => 'تنظيم حركة المرور وخدمات الترخيص.', 'contact_phone' => null, 'contact_email' => null],
                ['name' => 'مديرية شؤون السجون والاصلاح', 'description' => 'إدارة مراكز الاحتجاز وسجون الدولة.', 'contact_phone' => null, 'contact_email' => null],
            ];

            foreach ($interiorDirectorates as $d) {
                $insertMany[] = array_merge($d, [
                    'ministry_id' => $mid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        /*
         * 6) بقية الوزارات: مديريات شائعة/وظيفية تضمن تغطية أساسية لكل وزارة
         * (الإقتصاد، المالية، النقل، الثقافة، السياحة، الإعلام، الطاقة، الإدارة المحلية، الشباب والرياضة، العدل...).
         */
        $genericMinistries = [
            'وزارة الاقتصاد' => [
                'مديرية التجارة الداخلية', 'مديرية دعم وتنمية المشروعات', 'مديرية التخطيط الاقتصادي'
            ],
            'وزارة المالية' => [
                'مديرية الخزينة والموازنات', 'مديرية الضريبة المباشرة', 'مديرية الجمارك والتسعير'
            ],
            'وزارة النقل' => [
                'مديرية النقل البري', 'مديرية النقل البحري', 'مديرية الطيران المدني'
            ],
            'وزارة الثقافة' => [
                'مديرية التراث والمواقع التاريخية', 'مديرية الفنون المسرحية', 'مديرية النشر'
            ],
            'وزارة السياحة' => [
                'مديرية الترويج السياحي', 'مديرية التنظيم واللوائح', 'مديرية الترخيص الفندقي'
            ],
            'وزارة الإعلام' => [
                'مديرية الإعلام المرئي والمسموع', 'مديرية الصحافة والنشر', 'مديرية العلاقات العامة'
            ],
            'وزارة الطاقة' => [
                'مديرية الكهرباء', 'مديرية النفط والموارد', 'مديرية التخطيط الطاقي'
            ],
            'وزارة الإدارة المحلية والبيئة' => [
                'مديرية الشؤون البلدية', 'مديرية التخطيط العمراني', 'مديرية حماية البيئة'
            ],
            'وزارة الشباب والرياضة' => [
                'مديرية النشاطات الشبابية', 'مديرية المنشآت الرياضية', 'مديرية المسابقات والفرق'
            ],
            'وزارة العدل' => [
                'مديرية الشؤون القضائية', 'مديرية السجل العدلي', 'مديرية التنفيذ'
            ],
        ];

        foreach ($genericMinistries as $minNameStart => $dirs) {
            $mid = $getMinistryId($minNameStart);
            if (! $mid) continue;
            foreach ($dirs as $dname) {
                $insertMany[] = [
                    'ministry_id' => $mid,
                    'name' => $dname,
                    'description' => null,
                    'contact_phone' => null,
                    'contact_email' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert all gathered directorates
        if (!empty($insertMany)) {
            DB::table('directorates')->insert($insertMany);
        }
    }
}
