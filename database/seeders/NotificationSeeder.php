<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Shop;
use App\Models\Management;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin user
        $admin = Management::first();
        if (!$admin) {
            $admin = Management::create([
                'name' => 'Admin User',
                'email' => 'admin@digitalcaremlm.in',
                'phone' => '1234567890',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }

        // Get some users and shops
        $users = User::take(3)->get();
        $shops = Shop::take(2)->get();

        // Create sample notifications for users
        if ($users->count() > 0) {
            $notifications = [
                [
                    'title' => 'Welcome to Digital Care MLM!',
                    'message' => 'Thank you for joining our platform. Start referring friends and earning commissions today!',
                    'type' => 'success',
                    'recipient_type' => 'user',
                    'recipient_id' => $users->first()->id,
                    'created_by' => $admin->id,
                ],
                [
                    'title' => 'KYC Verification Required',
                    'message' => 'Please complete your KYC verification to unlock all features and start earning.',
                    'type' => 'warning',
                    'recipient_type' => 'user',
                    'recipient_id' => $users->first()->id,
                    'created_by' => $admin->id,
                ],
                [
                    'title' => 'New Commission Earned',
                    'message' => 'Congratulations! You have earned ₹500 in referral commissions this week.',
                    'type' => 'info',
                    'recipient_type' => 'user',
                    'recipient_id' => $users->first()->id,
                    'created_by' => $admin->id,
                ],
            ];

            foreach ($notifications as $notificationData) {
                Notification::create($notificationData);
            }
        }

        // Create sample notifications for shops
        if ($shops->count() > 0) {
            $shopNotifications = [
                [
                    'title' => 'Shop Registration Approved',
                    'message' => 'Your shop has been approved and is now live on our platform. Start receiving customers!',
                    'type' => 'success',
                    'recipient_type' => 'shop',
                    'recipient_id' => $shops->first()->id,
                    'created_by' => $admin->id,
                ],
                [
                    'title' => 'Commission Payment Ready',
                    'message' => 'Your commission payment of ₹1,200 is ready for withdrawal.',
                    'type' => 'info',
                    'recipient_type' => 'shop',
                    'recipient_id' => $shops->first()->id,
                    'created_by' => $admin->id,
                ],
            ];

            foreach ($shopNotifications as $notificationData) {
                Notification::create($notificationData);
            }
        }

        // Create a general announcement
        Notification::create([
            'title' => 'System Maintenance Notice',
            'message' => 'We will be performing scheduled maintenance on Sunday, 2:00 AM - 4:00 AM. Some features may be temporarily unavailable.',
            'type' => 'announcement',
            'recipient_type' => 'all',
            'recipient_id' => null,
            'created_by' => $admin->id,
        ]);
    }
}