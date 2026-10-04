<?php

namespace Database\Seeders;

use App\Enums\User\AgentVerificationStatus;
use App\Enums\User\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'moderator@propnest.test';

    public const AGENT_EMAIL = 'agent@propnest.test';

    public const BUYER_EMAIL = 'buyer@propnest.test';

    /**
     * email => [name, agency, phone, verification status, bio]
     */
    public const AGENTS = [
        self::AGENT_EMAIL => [
            'Maya Thompson', 'Lone Star Realty Group', '(512) 555-0142', AgentVerificationStatus::Verified,
            'Austin native with eleven years helping first-time buyers and growing families find homes across Central Texas and Dallas.',
        ],
        'daniel.reyes@example.com' => [
            'Daniel Reyes', 'Mile High Homes', '(303) 555-0187', AgentVerificationStatus::Verified,
            'Denver specialist in historic Highlands homes and new builds out east. Former contractor, so I know what to look for in an inspection.',
        ],
        'priya.natarajan@example.com' => [
            'Priya Natarajan', 'Emerald City Properties', '(206) 555-0123', AgentVerificationStatus::Verified,
            'I help tech relocations and investors navigate the Seattle market, from Capitol Hill condos to Ballard townhomes.',
        ],
        'marcus.bennett@example.com' => [
            'Marcus Bennett', 'Bayfront Realty', '(305) 555-0164', AgentVerificationStatus::Verified,
            'Luxury condos in Brickell, family homes in Orlando. Bilingual (English/Spanish) and available seven days a week.',
        ],
        'elena.petrova@example.com' => [
            'Elena Petrova', 'North Shore Realty Co.', '(312) 555-0119', AgentVerificationStatus::Verified,
            'Chicago agent focused on Lincoln Park, Wicker Park and the West Loop. Commercial leasing experience for small businesses.',
        ],
        'jordan.hayes@example.com' => [
            'Jordan Hayes', 'Pacific Crest Realty', '(619) 555-0108', AgentVerificationStatus::Pending,
            'New to PropNest, not new to San Diego. Bungalows and craftsman homes in North Park, Hillcrest and Mission Hills.',
        ],
        'sofia.alvarez@example.com' => [
            'Sofia Alvarez', 'Independent Agent', '(512) 555-0176', AgentVerificationStatus::Unverified,
            'Independent agent covering Austin rentals.',
        ],
    ];

    public const BUYERS = [
        self::BUYER_EMAIL => 'Alex Morgan',
        'chris.walker@example.com' => 'Chris Walker',
        'hannah.lee@example.com' => 'Hannah Lee',
        'omar.siddiqui@example.com' => 'Omar Siddiqui',
        'grace.kim@example.com' => 'Grace Kim',
        'ethan.brooks@example.com' => 'Ethan Brooks',
        'lily.chen@example.com' => 'Lily Chen',
        'noah.patel@example.com' => 'Noah Patel',
        'ava.martinez@example.com' => 'Ava Martinez',
        'lucas.wright@example.com' => 'Lucas Wright',
        'zara.ahmed@example.com' => 'Zara Ahmed',
        'ryan.oconnor@example.com' => "Ryan O'Connor",
        'mia.johnson@example.com' => 'Mia Johnson',
        'ben.carter@example.com' => 'Ben Carter',
        'chloe.nguyen@example.com' => 'Chloe Nguyen',
    ];

    public const SUSPENDED_EMAIL = 'ben.carter@example.com';

    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Nadia Karim',
            'email' => self::ADMIN_EMAIL,
            'created_at' => now()->subDays(360),
            'updated_at' => now()->subDays(360),
            'last_login_at' => now()->subHours(3),
        ]);

        foreach (self::AGENTS as $email => [$name, $agency, $phone, $verification, $bio]) {
            $joined = now()->subDays(mt_rand(330, 355));

            // UserObserver creates the empty agent profile; fill it in.
            $agent = User::factory()->agent()->create([
                'name' => $name,
                'email' => $email,
                'created_at' => $joined,
                'updated_at' => $joined,
                'last_login_at' => now()->subHours(mt_rand(1, 96)),
            ]);

            $agent->agentProfile->update([
                'agency_name' => $agency,
                'phone' => $phone,
                'verification_status' => $verification,
                'bio' => $bio,
            ]);
        }

        foreach (self::BUYERS as $email => $name) {
            // Spread sign-ups over the last year so the growth chart has a shape.
            $joined = $email === self::BUYER_EMAIL
                ? now()->subDays(300)
                : now()->subDays(mt_rand(5, 330))->subMinutes(mt_rand(0, 1440));

            User::factory()->create([
                'name' => $name,
                'email' => $email,
                'created_at' => $joined,
                'updated_at' => $joined,
                'last_login_at' => now()->subHours(mt_rand(1, 400)),
            ]);
        }

        User::where('email', self::SUSPENDED_EMAIL)->firstOrFail()->update([
            'status' => UserStatus::Suspended,
            'suspended_at' => now()->subDays(9),
            'suspension_reason' => 'Sent the same promotional message to more than 20 agents.',
        ]);
    }
}
