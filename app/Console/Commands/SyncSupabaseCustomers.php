<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\SupabaseService;
use Illuminate\Console\Command;
use Throwable;

class SyncSupabaseCustomers extends Command
{
    protected $signature = 'supabase:sync-customers';

    protected $description =
        'Sync customers from Supabase to Laravel customers table';

    public function handle(
        SupabaseService $supabase
    ): int {
        $this->info(
            'Starting Supabase customers synchronization...'
        );

        try {

            /*
             * -------------------------------------------------
             * 1. Get profiles
             * -------------------------------------------------
             */

            $profiles = [];

            $offset = 0;
            $limit = 1000;

            do {

                $batch = $supabase->getProfiles(
                    $offset,
                    $limit
                );

                foreach ($batch as $profile) {
                    if (!empty($profile['id'])) {
                        $profiles[$profile['id']] = $profile;
                    }
                }

                $count = count($batch);

                $offset += $limit;

            } while ($count === $limit);


            $this->info(
                'Profiles found: ' . count($profiles)
            );


            /*
             * -------------------------------------------------
             * 2. Get users from Supabase Auth
             * -------------------------------------------------
             */

            $users = [];

            $page = 1;
            $perPage = 100;

            do {

                $batch = $supabase->getUsers(
                    $page,
                    $perPage
                );

                foreach ($batch as $user) {
                    if (!empty($user['id'])) {
                        $users[$user['id']] = $user;
                    }
                }

                $count = count($batch);

                $page++;

            } while ($count === $perPage);


            $this->info(
                'Auth users found: ' . count($users)
            );


            /*
             * -------------------------------------------------
             * 3. Merge Auth users + profiles
             * -------------------------------------------------
             */

            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($users as $userId => $user) {

                /*
                 * Email comes from auth.users
                 */
                $email = $user['email'] ?? null;

                /*
                 * Profile data comes from public.profiles
                 */
                $profile = $profiles[$userId] ?? null;

                if (!$profile) {
                    $this->warn(
                        "Profile not found for user: {$userId}"
                    );

                    $skipped++;

                    continue;
                }

                if (empty($email)) {
                    $this->warn(
                        "Email not found for user: {$userId}"
                    );

                    $skipped++;

                    continue;
                }


                /*
                 * Prepare customer data
                 */

                $data = [
                    'name' => $profile['user_name']
                        ?? 'Customer',

                    'email' => $email,

                    'phone' => $profile['phone_number']
                        ?? null,

                    'profile_image' => $profile['avatar_url']
                        ?? null,
                ];


                /*
                 * Check whether customer already exists
                 */

                $customer = Customer::where(
                    'supabase_id',
                    $userId
                )->first();


                if ($customer) {

                    $customer->update($data);

                    $updated++;

                } else {

                    Customer::create([
                        'supabase_id' => $userId,
                        ...$data,
                    ]);

                    $created++;
                }
            }


            /*
             * -------------------------------------------------
             * 4. Result
             * -------------------------------------------------
             */

            $this->newLine();

            $this->info(
                "Created: {$created}"
            );

            $this->info(
                "Updated: {$updated}"
            );

            $this->info(
                "Skipped: {$skipped}"
            );

            $this->newLine();

            $this->info(
                'Supabase customers synchronization completed successfully.'
            );

            return self::SUCCESS;

        } catch (Throwable $e) {

            $this->error(
                'Synchronization failed.'
            );

            $this->error(
                $e->getMessage()
            );

            return self::FAILURE;
        }
    }
}