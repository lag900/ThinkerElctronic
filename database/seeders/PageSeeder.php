<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'privacy',
                'title_en' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'content_en' => '<h2>1. Information We Collect</h2><p>We collect information you provide directly to us...</p>',
                'content_ar' => '<h2>1. المعلومات التي نجمعها</h2><p>نقوم بجمع المعلومات التي تقدمها لنا مباشرة...</p>',
            ],
            [
                'slug' => 'terms',
                'title_en' => 'Terms of Service',
                'title_ar' => 'شروط الخدمة',
                'content_en' => '<h2>1. Acceptance of Terms</h2><p>By accessing or using Thinker\'s website...</p>',
                'content_ar' => '<h2>1. قبول الشروط</h2><p>من خلال الوصول إلى موقع Thinker واستخدامه...</p>',
            ],
            [
                'slug' => 'refund-policy',
                'title_en' => 'Refund Policy',
                'title_ar' => 'سياسة الاسترجاع',
                'content_en' => '<h2>No Refund Policy</h2><p>All sales are final...</p>',
                'content_ar' => '<h2>سياسة عدم الاسترجاع</h2><p>جميع المبيعات نهائية...</p>',
            ],
            [
                'slug' => 'contact',
                'title_en' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'content_en' => '<p>Get in touch with our team...</p>',
                'content_ar' => '<p>تواصل مع فريقنا...</p>',
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
