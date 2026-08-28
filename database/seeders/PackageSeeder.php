<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Seed the starter package catalog. Idempotent by slug — safe to re-run;
     * it only fills in packages that don't exist yet, so any pricing or
     * copy you've since edited in the admin UI is never overwritten.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Simple Static Site',
                'slug' => 'simple-static-site',
                'category' => 'static',
                'description' => 'A clean, fast marketing site for a business that just needs to be found and trusted online.',
                'price' => 1500,
                'sort_order' => 0,
                'features' => [
                    'Responsive design',
                    'Up to 5 pages',
                    'Contact form',
                    'Basic SEO setup',
                    'Fast page loads',
                ],
            ],
            [
                'name' => 'Complete Static + Auth',
                'slug' => 'complete-static-auth',
                'category' => 'static_auth',
                'description' => 'Everything in Simple Static, plus accounts so visitors can log in and see something just for them.',
                'price' => 3000,
                'sort_order' => 1,
                'features' => [
                    'Everything in Simple Static',
                    'User registration & login',
                    'Password reset',
                    'Protected member-only pages',
                    'Basic user profile',
                ],
            ],
            [
                'name' => 'Social Network Features',
                'slug' => 'social-network-features',
                'category' => 'social',
                'description' => 'The building blocks of a social platform — profiles, feeds, and ways for people to connect.',
                'price' => 9000,
                'sort_order' => 2,
                'features' => [
                    'User profiles',
                    'Activity feed',
                    'Follow / friend system',
                    'Direct messaging',
                    'Notifications',
                    'Likes & comments',
                ],
            ],
            [
                'name' => 'Media Platform',
                'slug' => 'media-platform',
                'category' => 'media',
                'description' => 'Upload, organize, and stream media content to your audience.',
                'price' => 6000,
                'sort_order' => 3,
                'features' => [
                    'Media uploads (video, audio, image)',
                    'Streaming playback',
                    'Categories & tagging',
                    'Search',
                    'Basic analytics',
                ],
            ],
            [
                'name' => 'E-commerce Store',
                'slug' => 'ecommerce-store',
                'category' => 'ecommerce',
                'description' => 'A full online store, from product catalog to checkout.',
                'price' => 7500,
                'sort_order' => 4,
                'features' => [
                    'Product catalog',
                    'Cart & checkout',
                    'Stripe payments',
                    'Order management',
                    'Inventory tracking',
                    'Customer accounts',
                ],
            ],
            [
                'name' => 'Staff/Client Portal',
                'slug' => 'staff-client-portal',
                'category' => 'portal',
                'description' => 'A private workspace for your team and customers — the same kind of system powering this site.',
                'price' => 12000,
                'sort_order' => 5,
                'features' => [
                    'Role-based access (staff/admin/customer)',
                    'Customer dashboard',
                    'Admin backend',
                    'Notifications',
                    'Secure messaging',
                ],
            ],
        ];

        foreach ($packages as $package) {
            Package::firstOrCreate(
                ['slug' => $package['slug']],
                $package,
            );
        }
    }
}
