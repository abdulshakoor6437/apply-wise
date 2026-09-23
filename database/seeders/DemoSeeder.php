<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Application;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@jobtracker.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Clear existing demo user data
        $user->applications()->delete();
        $user->companies()->delete();
        $user->documents()->delete();

        // 1. Companies (5 Companies)
        $google = Company::create([
            'user_id' => $user->id,
            'name' => 'Google',
            'website' => 'https://google.com',
            'industry' => 'Technology',
            'size' => 'Enterprise',
            'location' => 'Mountain View, CA',
            'notes' => 'Cloud Infrastructure engineering team.',
        ]);

        $stripe = Company::create([
            'user_id' => $user->id,
            'name' => 'Stripe',
            'website' => 'https://stripe.com',
            'industry' => 'Fintech',
            'size' => 'Large',
            'location' => 'San Francisco, CA',
            'notes' => 'Financial infrastructure platform.',
        ]);

        $notion = Company::create([
            'user_id' => $user->id,
            'name' => 'Notion',
            'website' => 'https://notion.so',
            'industry' => 'SaaS',
            'size' => 'Medium',
            'location' => 'New York, NY',
            'notes' => 'Workspace & productivity suite.',
        ]);

        $vercel = Company::create([
            'user_id' => $user->id,
            'name' => 'Vercel',
            'website' => 'https://vercel.com',
            'industry' => 'Developer Tools',
            'size' => 'Small',
            'location' => 'San Francisco, CA',
            'notes' => 'Frontend cloud platform.',
        ]);

        $anthropic = Company::create([
            'user_id' => $user->id,
            'name' => 'Anthropic',
            'website' => 'https://anthropic.com',
            'industry' => 'AI',
            'size' => 'Medium',
            'location' => 'San Francisco, CA',
            'notes' => 'AI research & Claude deployment team.',
        ]);

        // 2. Contacts (4-5 Contacts)
        Contact::create([
            'user_id' => $user->id,
            'company_id' => $stripe->id,
            'name' => 'Sarah Jenkins',
            'role' => 'Senior Tech Recruiter',
            'email' => 'sjenkins@stripe.com',
            'phone' => '+1 (415) 555-0142',
            'linkedin_url' => 'https://linkedin.com/in/sarahjenkins-recruiter',
            'notes' => 'Initial recruiter point of contact for Fullstack team.',
        ]);

        Contact::create([
            'user_id' => $user->id,
            'company_id' => $vercel->id,
            'name' => 'Alex Rivera',
            'role' => 'Engineering Manager',
            'email' => 'arivera@vercel.com',
            'phone' => '+1 (415) 555-0199',
            'linkedin_url' => 'https://linkedin.com/in/alexrivera-eng',
            'notes' => 'Hiring manager for Core Platform team.',
        ]);

        Contact::create([
            'user_id' => $user->id,
            'company_id' => $anthropic->id,
            'name' => 'Elena Rostova',
            'role' => 'Technical Sourcer',
            'email' => 'erostova@anthropic.com',
            'phone' => '+1 (415) 555-0311',
            'linkedin_url' => 'https://linkedin.com/in/elenarostova',
            'notes' => 'Reached out on LinkedIn for Infrastructure role.',
        ]);

        // 3. Documents (3 Documents)
        $resume1 = Document::create([
            'user_id' => $user->id,
            'label' => 'Resume v1 — General',
            'file_path' => 'documents/resume_v1_general.pdf',
            'original_filename' => 'resume_v1_general.pdf',
            'doc_type' => 'resume',
            'mime_type' => 'application/pdf',
            'file_size' => 245000,
        ]);

        $resume2 = Document::create([
            'user_id' => $user->id,
            'label' => 'Resume v2 — Backend Focus',
            'file_path' => 'documents/resume_v2_backend.pdf',
            'original_filename' => 'resume_v2_backend.pdf',
            'doc_type' => 'resume',
            'mime_type' => 'application/pdf',
            'file_size' => 310000,
        ]);

        $coverLetter = Document::create([
            'user_id' => $user->id,
            'label' => 'Cover Letter — Tech',
            'file_path' => 'documents/cover_letter_tech.pdf',
            'original_filename' => 'cover_letter_tech.pdf',
            'doc_type' => 'cover_letter',
            'file_size' => 120000,
        ]);

        // 4. Applications (9 Applications spread across pipeline stages)
        $apps = [
            // Wishlist (2)
            [
                'company' => $anthropic,
                'title' => 'AI Infrastructure Engineer',
                'status' => 'wishlist',
                'source' => 'LinkedIn',
                'salary_min' => 200000,
                'salary_max' => 250000,
                'excitement' => 5,
                'applied' => null,
                'next_action' => now()->addDays(3),
                'notes' => 'Tailoring resume for LLM inference optimization experience.',
            ],
            [
                'company' => $notion,
                'title' => 'Senior Frontend Developer',
                'status' => 'wishlist',
                'source' => 'Company Website',
                'salary_min' => 170000,
                'salary_max' => 210000,
                'excitement' => 4,
                'applied' => null,
                'next_action' => now()->addDays(5),
                'notes' => 'Preparing portfolio highlighting rich text editor architecture.',
            ],

            // Applied (2)
            [
                'company' => $google,
                'title' => 'Cloud Systems Developer',
                'status' => 'applied',
                'source' => 'Referral',
                'salary_min' => 190000,
                'salary_max' => 240000,
                'excitement' => 5,
                'applied' => now()->subDays(5)->toDateString(),
                'next_action' => now()->addDays(4),
                'notes' => 'Submitted via employee referral link.',
            ],
            [
                'company' => $notion,
                'title' => 'Backend Engineer - Data Infrastructure',
                'status' => 'applied',
                'source' => 'Indeed',
                'salary_min' => 165000,
                'salary_max' => 200000,
                'excitement' => 4,
                'applied' => now()->subDays(9)->toDateString(),
                'next_action' => null,
                'notes' => 'Standard application submitted online.',
            ],

            // Phone Screen (1)
            [
                'company' => $vercel,
                'title' => 'Senior Platform Developer',
                'status' => 'phone_screen',
                'source' => 'LinkedIn',
                'salary_min' => 180000,
                'salary_max' => 220000,
                'excitement' => 5,
                'applied' => now()->subWeeks(2)->toDateString(),
                'next_action' => now()->addDays(1),
                'notes' => 'Screen complete with Alex Rivera. Recruiter scheduling technical interview.',
            ],

            // Interview (2)
            [
                'company' => $stripe,
                'title' => 'Senior Software Engineer',
                'status' => 'interview',
                'source' => 'LinkedIn',
                'salary_min' => 185000,
                'salary_max' => 230000,
                'excitement' => 5,
                'applied' => now()->subWeeks(4)->toDateString(),
                'next_action' => now()->addDays(2),
                'notes' => 'Passed recruiter call & coding assessment. Onsite architecture interview scheduled.',
            ],
            [
                'company' => $anthropic,
                'title' => 'Staff Backend Engineer',
                'status' => 'interview',
                'source' => 'Referral',
                'salary_min' => 220000,
                'salary_max' => 270000,
                'excitement' => 5,
                'applied' => now()->subWeeks(3)->toDateString(),
                'next_action' => now()->addDays(4),
                'notes' => 'Passed technical phone interview. System design round upcoming.',
            ],

            // Offer (1)
            [
                'company' => $vercel,
                'title' => 'Fullstack Tech Lead',
                'status' => 'offer',
                'source' => 'Company Website',
                'salary_min' => 195000,
                'salary_max' => 240000,
                'excitement' => 5,
                'applied' => now()->subWeeks(6)->toDateString(),
                'next_action' => now()->addDays(3),
                'notes' => 'Written offer received: $215,000 base + equity grant.',
            ],

            // Rejected (1)
            [
                'company' => $google,
                'title' => 'Staff Architect',
                'status' => 'rejected',
                'source' => 'Other',
                'salary_min' => 210000,
                'salary_max' => 260000,
                'excitement' => 4,
                'applied' => now()->subWeeks(8)->toDateString(),
                'next_action' => null,
                'notes' => 'Received formal update after final round.',
            ],
        ];

        foreach ($apps as $idx => $data) {
            $app = Application::create([
                'user_id' => $user->id,
                'company_id' => $data['company']->id,
                'job_title' => $data['title'],
                'status' => $data['status'],
                'source' => $data['source'],
                'job_posting_url' => $data['company']->website . '/careers/' . Str::slug($data['title']),
                'salary_min' => $data['salary_min'],
                'salary_max' => $data['salary_max'],
                'excitement_rating' => $data['excitement'],
                'priority' => $idx,
                'date_applied' => $data['applied'],
                'next_action_date' => $data['next_action'],
                'notes' => $data['notes'],
            ]);

            // Attach documents
            if ($idx % 2 === 0) {
                $app->documents()->attach($resume1->id);
            } else {
                $app->documents()->attach($resume2->id);
                $app->documents()->attach($coverLetter->id);
            }

            // Create activities
            if ($data['status'] !== 'wishlist') {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'wishlist',
                    'to_status' => 'applied',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied']) : now()->subWeeks(3),
                ]);
            }

            if (in_array($data['status'], ['phone_screen', 'interview', 'offer', 'rejected'])) {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'applied',
                    'to_status' => 'phone_screen',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied'])->addDays(4) : now()->subWeeks(2),
                ]);
            }

            if (in_array($data['status'], ['interview', 'offer'])) {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'phone_screen',
                    'to_status' => 'interview',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied'])->addDays(10) : now()->subWeeks(1),
                ]);
            }

            if ($data['status'] === 'offer') {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'interview',
                    'to_status' => 'offer',
                    'created_at' => now()->subDays(2),
                ]);
            }

            // Manual activity notes
            if ($idx === 0) {
                Activity::create([
                    'application_id' => $app->id,
                    'notes' => 'Sent follow-up email to recruiter.',
                    'created_at' => now()->subDays(1),
                ]);
            } else if ($idx === 4) {
                Activity::create([
                    'application_id' => $app->id,
                    'notes' => 'Recruiter mentioned team is actively expanding infrastructure headcount.',
                    'created_at' => now()->subHours(12),
                ]);
            }

            // Create Interviews (3-4 Interviews)
            if ($data['status'] === 'interview') {
                Interview::create([
                    'application_id' => $app->id,
                    'round_type' => 'technical',
                    'scheduled_at' => now()->addDays(2),
                    'outcome' => 'pending',
                    'prep_notes' => 'Technical coding & distributed systems architecture.',
                ]);
            } else if ($data['status'] === 'offer') {
                Interview::create([
                    'application_id' => $app->id,
                    'round_type' => 'final',
                    'scheduled_at' => now()->subDays(4),
                    'outcome' => 'passed',
                    'prep_notes' => 'VP of Engineering interview passed.',
                ]);
            } else if ($data['status'] === 'phone_screen') {
                Interview::create([
                    'application_id' => $app->id,
                    'round_type' => 'phone_screen',
                    'scheduled_at' => now()->subDays(2),
                    'outcome' => 'passed',
                    'prep_notes' => 'Initial screening conversation.',
                ]);
            }
        }

        // 5. Test Unread Notifications
        DatabaseNotification::create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\InterviewReminderNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'interview',
                'title' => 'Upcoming Technical Interview',
                'message' => 'Your technical interview with Stripe is scheduled in 2 days.',
            ],
            'read_at' => null,
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]);
    }
}
