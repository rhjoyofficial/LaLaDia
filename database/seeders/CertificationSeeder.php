<?php

namespace Database\Seeders;

use App\Domains\Certification\Models\Certification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Certification::truncate();
        Schema::enableForeignKeyConstraints();

        $certifications = [
            [
                'name' => 'ISO 22000',
                'category' => 'Food Safety Management',
                'organization' => 'International Organization for Standardization',
                'additional_details' => 'Global standard for food safety management systems across the food chain.',
                'logo_path' => 'certifications/iso.png',
                'image_path' => 'certifications/iso.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Halal Certified',
                'category' => 'Dietary Standards',
                'organization' => 'Islamic Foundation Bangladesh',
                'additional_details' => 'Ensures all ingredients and production processes comply with Islamic dietary laws.',
                'logo_path' => 'certifications/halal.png',
                'image_path' => 'certifications/halal.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'HACCP Certified',
                'category' => 'Food Safety',
                'organization' => 'Food Safety Solutions',
                'additional_details' => 'Hazard Analysis and Critical Control Points — ensuring systematic preventive approach to food safety.',
                'logo_path' => 'certifications/haccp.png',
                'image_path' => 'certifications/haccp.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'GMP Quality Standards',
                'category' => 'Manufacturing Quality',
                'organization' => 'Quality Assurance Board',
                'additional_details' => 'Good Manufacturing Practice — ensuring products are consistently produced and controlled according to quality standards.',
                'logo_path' => 'certifications/gmp.png',
                'image_path' => 'certifications/gmp.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($certifications as $cert) {
            Certification::create($cert);
        }
    }
}
