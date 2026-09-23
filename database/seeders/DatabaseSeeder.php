<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Clear existing data for a fresh seed
        $user->applications()->delete();
        $user->companies()->delete();
        $user->documents()->delete();

        // 1. Create 10 Companies
        $companies = [
            'stripe' => Company::create([
                'user_id' => $user->id,
                'name' => 'Stripe',
                'website' => 'https://stripe.com',
                'industry' => 'Fintech / Payments',
                'size' => '1000-5000',
                'location' => 'San Francisco, CA',
                'notes' => 'Top choice payments infrastructure company. Great engineering culture.',
            ]),
            'google' => Company::create([
                'user_id' => $user->id,
                'name' => 'Google',
                'website' => 'https://google.com',
                'industry' => 'Search & Cloud',
                'size' => '10000+',
                'location' => 'Mountain View, CA',
                'notes' => 'Applied for Cloud Infrastructure team.',
            ]),
            'meta' => Company::create([
                'user_id' => $user->id,
                'name' => 'Meta',
                'website' => 'https://meta.com',
                'industry' => 'Social Media & AI',
                'size' => '10000+',
                'location' => 'Menlo Park, CA',
                'notes' => 'Product Engineering team.',
            ]),
            'acme' => Company::create([
                'user_id' => $user->id,
                'name' => 'Acme Corp',
                'website' => 'https://acme.com',
                'industry' => 'Enterprise Software',
                'size' => '500-1000',
                'location' => 'Austin, TX',
                'notes' => 'High-growth B2B SaaS platform.',
            ]),
            'vercel' => Company::create([
                'user_id' => $user->id,
                'name' => 'Vercel',
                'website' => 'https://vercel.com',
                'industry' => 'Cloud & Frontend Tools',
                'size' => '250-500',
                'location' => 'Remote',
                'notes' => 'Creators of Next.js. Remote-first environment.',
            ]),
            'datadog' => Company::create([
                'user_id' => $user->id,
                'name' => 'Datadog',
                'website' => 'https://datadoghq.com',
                'industry' => 'Observability & Security',
                'size' => '1000-5000',
                'location' => 'New York, NY',
                'notes' => 'Monitoring & telemetry platform.',
            ]),
            'figma' => Company::create([
                'user_id' => $user->id,
                'name' => 'Figma',
                'website' => 'https://figma.com',
                'industry' => 'Design & Collaboration',
                'size' => '500-1000',
                'location' => 'San Francisco, CA',
                'notes' => 'Interface design platform.',
            ]),
            'linear' => Company::create([
                'user_id' => $user->id,
                'name' => 'Linear',
                'website' => 'https://linear.app',
                'industry' => 'Developer Tools',
                'size' => '50-100',
                'location' => 'Remote',
                'notes' => 'Issue tracking built for modern software teams.',
            ]),
            'supabase' => Company::create([
                'user_id' => $user->id,
                'name' => 'Supabase',
                'website' => 'https://supabase.com',
                'industry' => 'Open Source Database',
                'size' => '100-250',
                'location' => 'Remote',
                'notes' => 'Open source Firebase alternative.',
            ]),
            'cloudflare' => Company::create([
                'user_id' => $user->id,
                'name' => 'Cloudflare',
                'website' => 'https://cloudflare.com',
                'industry' => 'Edge Computing & Security',
                'size' => '1000-5000',
                'location' => 'Austin, TX',
                'notes' => 'Global network platform.',
            ]),
        ];

        // Contacts
        Contact::create([
            'user_id' => $user->id,
            'company_id' => $companies['stripe']->id,
            'name' => 'Sarah Jenkins',
            'role' => 'Senior Tech Recruiter',
            'email' => 'sjenkins@stripe.com',
            'phone' => '+1 (415) 555-0142',
            'linkedin_url' => 'https://linkedin.com/in/sarahjenkins-recruiter',
            'notes' => 'Initial recruiter touchpoint for Fullstack team.',
        ]);

        Contact::create([
            'user_id' => $user->id,
            'company_id' => $companies['vercel']->id,
            'name' => 'Alex Rivera',
            'role' => 'Engineering Manager',
            'email' => 'arivera@vercel.com',
            'phone' => '+1 (415) 555-0199',
            'linkedin_url' => 'https://linkedin.com/in/alexrivera-eng',
            'notes' => 'Hiring manager for Core Platform.',
        ]);

        // 2. Documents
        $resume1 = Document::create([
            'user_id' => $user->id,
            'label' => 'Software Engineer Resume (2026)',
            'file_path' => 'documents/resume_software_engineer.pdf',
            'original_filename' => 'resume_software_engineer.pdf',
            'mime_type' => 'application/pdf',
            'doc_type' => 'resume',
            'file_size' => 245000,
        ]);

        $resume2 = Document::create([
            'user_id' => $user->id,
            'label' => 'Fullstack Tech Lead Resume',
            'file_path' => 'documents/resume_tech_lead.pdf',
            'original_filename' => 'resume_tech_lead.pdf',
            'mime_type' => 'application/pdf',
            'doc_type' => 'resume',
            'file_size' => 310000,
        ]);

        $coverLetter = Document::create([
            'user_id' => $user->id,
            'label' => 'General Tech Cover Letter',
            'file_path' => 'documents/cover_letter_general.pdf',
            'original_filename' => 'cover_letter_general.pdf',
            'mime_type' => 'application/pdf',
            'doc_type' => 'cover_letter',
            'file_size' => 120000,
        ]);

        // 3. Applications (18 Total across all pipeline stages)
        $appsData = [
            // Wishlist (3)
            [
                'company' => $companies['figma'],
                'title' => 'Senior Frontend Engineer',
                'status' => 'wishlist',
                'source' => 'LinkedIn',
                'salary_min' => 180000,
                'salary_max' => 220000,
                'excitement' => 5,
                'applied' => null,
                'next_action' => now()->addDays(3),
                'notes' => 'Referred by Dave. Need to tailor resume for WebGL/Canvas experience.',
            ],
            [
                'company' => $companies['cloudflare'],
                'title' => 'Systems Engineer - Workers',
                'status' => 'wishlist',
                'source' => 'Company Website',
                'salary_min' => 160000,
                'salary_max' => 195000,
                'excitement' => 4,
                'applied' => null,
                'next_action' => now()->addDays(5),
                'notes' => 'Interesting Rust / V8 isolate architecture.',
            ],
            [
                'company' => $companies['supabase'],
                'title' => 'Developer Advocate',
                'status' => 'wishlist',
                'source' => 'Twitter / X',
                'salary_min' => 140000,
                'salary_max' => 175000,
                'excitement' => 3,
                'applied' => null,
                'next_action' => null,
                'notes' => 'Open source community role.',
            ],

            // Applied (4)
            [
                'company' => $companies['google'],
                'title' => 'Backend Infrastructure Developer',
                'status' => 'applied',
                'source' => 'LinkedIn',
                'salary_min' => 190000,
                'salary_max' => 240000,
                'excitement' => 5,
                'applied' => now()->subDays(4)->toDateString(),
                'next_action' => now()->addDays(6),
                'notes' => 'Submitted via referral link from former colleague.',
            ],
            [
                'company' => $companies['linear'],
                'title' => 'Product Engineer',
                'status' => 'applied',
                'source' => 'Company Website',
                'salary_min' => 170000,
                'salary_max' => 210000,
                'excitement' => 5,
                'applied' => now()->subDays(6)->toDateString(),
                'next_action' => now()->addDays(2),
                'notes' => 'Loves their product UI. Submitted portfolio alongside resume.',
            ],
            [
                'company' => $companies['datadog'],
                'title' => 'Software Engineer - Telemetry',
                'status' => 'applied',
                'source' => 'Indeed',
                'salary_min' => 155000,
                'salary_max' => 190000,
                'excitement' => 4,
                'applied' => now()->subDays(8)->toDateString(),
                'next_action' => null,
                'notes' => 'Standard application submitted.',
            ],
            [
                'company' => $companies['acme'],
                'title' => 'Fullstack Developer',
                'status' => 'applied',
                'source' => 'Other',
                'salary_min' => 140000,
                'salary_max' => 165000,
                'excitement' => 3,
                'applied' => now()->subDays(10)->toDateString(),
                'next_action' => null,
                'notes' => 'Applied via regional tech job board.',
            ],

            // Phone Screen (3)
            [
                'company' => $companies['meta'],
                'title' => 'Lead Software Engineer',
                'status' => 'phone_screen',
                'source' => 'Company Website',
                'salary_min' => 210000,
                'salary_max' => 260000,
                'excitement' => 5,
                'applied' => now()->subWeeks(2)->toDateString(),
                'next_action' => now()->addDays(1),
                'notes' => 'Recruiter screen went great. Discussed distributed systems background.',
            ],
            [
                'company' => $companies['vercel'],
                'title' => 'Senior Platform Engineer',
                'status' => 'phone_screen',
                'source' => 'LinkedIn',
                'salary_min' => 185000,
                'salary_max' => 225000,
                'excitement' => 5,
                'applied' => now()->subWeeks(3)->toDateString(),
                'next_action' => now()->addDays(3),
                'notes' => 'Initial screening call scheduled with Alex Rivera.',
            ],
            [
                'company' => $companies['stripe'],
                'title' => 'Staff Backend Engineer',
                'status' => 'phone_screen',
                'source' => 'Referral',
                'salary_min' => 220000,
                'salary_max' => 270000,
                'excitement' => 5,
                'applied' => now()->subWeeks(2)->toDateString(),
                'next_action' => null,
                'notes' => 'Screen complete. Awaiting scheduling for technical round.',
            ],

            // Interview (4)
            [
                'company' => $companies['stripe'],
                'title' => 'Senior Software Engineer',
                'status' => 'interview',
                'source' => 'LinkedIn',
                'salary_min' => 185000,
                'salary_max' => 230000,
                'excitement' => 5,
                'applied' => now()->subWeeks(4)->toDateString(),
                'next_action' => now()->addDays(2),
                'notes' => 'Passed technical screen. System architecture interview upcoming.',
            ],
            [
                'company' => $companies['vercel'],
                'title' => 'Core Systems Developer',
                'status' => 'interview',
                'source' => 'Company Website',
                'salary_min' => 190000,
                'salary_max' => 235000,
                'excitement' => 5,
                'applied' => now()->subWeeks(5)->toDateString(),
                'next_action' => now()->addDays(4),
                'notes' => 'Onsite round (4 technical sessions) scheduled.',
            ],
            [
                'company' => $companies['figma'],
                'title' => 'Performance Engineer',
                'status' => 'interview',
                'source' => 'Referral',
                'salary_min' => 195000,
                'salary_max' => 240000,
                'excitement' => 4,
                'applied' => now()->subWeeks(6)->toDateString(),
                'next_action' => null,
                'notes' => 'Completed 2 technical rounds. Final behavioral round coming up.',
            ],
            [
                'company' => $companies['datadog'],
                'title' => 'Senior Backend Developer',
                'status' => 'interview',
                'source' => 'Indeed',
                'salary_min' => 175000,
                'salary_max' => 215000,
                'excitement' => 4,
                'applied' => now()->subWeeks(5)->toDateString(),
                'next_action' => null,
                'notes' => 'Coding challenge submitted and approved.',
            ],

            // Offer (2)
            [
                'company' => $companies['acme'],
                'title' => 'Staff Fullstack Engineer',
                'status' => 'offer',
                'source' => 'LinkedIn',
                'salary_min' => 175000,
                'salary_max' => 205000,
                'excitement' => 4,
                'applied' => now()->subWeeks(7)->toDateString(),
                'next_action' => now()->addDays(5),
                'notes' => 'Written offer received: $190,000 base + 15% bonus + equity.',
            ],
            [
                'company' => $companies['linear'],
                'title' => 'Senior Product Developer',
                'status' => 'offer',
                'source' => 'Company Website',
                'salary_min' => 180000,
                'salary_max' => 220000,
                'excitement' => 5,
                'applied' => now()->subWeeks(8)->toDateString(),
                'next_action' => now()->addDays(3),
                'notes' => 'Verbal offer extended! Reviewing equity grant package.',
            ],

            // Accepted (1)
            [
                'company' => $companies['stripe'],
                'title' => 'Lead Fullstack Engineer',
                'status' => 'accepted',
                'source' => 'Referral',
                'salary_min' => 210000,
                'salary_max' => 250000,
                'excitement' => 5,
                'applied' => now()->subWeeks(10)->toDateString(),
                'next_action' => null,
                'notes' => 'Offer accepted! Start date set for next month.',
            ],

            // Rejected (1)
            [
                'company' => $companies['google'],
                'title' => 'Staff Systems Architect',
                'status' => 'rejected',
                'source' => 'LinkedIn',
                'salary_min' => 220000,
                'salary_max' => 280000,
                'excitement' => 5,
                'applied' => now()->subWeeks(9)->toDateString(),
                'next_action' => null,
                'notes' => 'Decided to move forward with internal candidate after onsite.',
            ],
        ];

        foreach ($appsData as $idx => $data) {
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

            // Link resume document
            if ($idx % 2 === 0) {
                $app->documents()->attach($resume1->id);
            } else if ($idx % 3 === 0) {
                $app->documents()->attach($resume2->id);
                $app->documents()->attach($coverLetter->id);
            }

            // Create activities according to status
            if ($data['status'] !== 'wishlist') {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'wishlist',
                    'to_status' => 'applied',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied']) : now()->subWeeks(4),
                ]);
            }

            if (in_array($data['status'], ['phone_screen', 'interview', 'offer', 'accepted', 'rejected'])) {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'applied',
                    'to_status' => 'phone_screen',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied'])->addDays(5) : now()->subWeeks(3),
                ]);
            }

            if (in_array($data['status'], ['interview', 'offer', 'accepted'])) {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'phone_screen',
                    'to_status' => 'interview',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied'])->addDays(12) : now()->subWeeks(2),
                ]);
            }

            if (in_array($data['status'], ['offer', 'accepted'])) {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'interview',
                    'to_status' => 'offer',
                    'created_at' => $data['applied'] ? Carbon::parse($data['applied'])->addDays(20) : now()->subWeeks(1),
                ]);
            }

            if ($data['status'] === 'accepted') {
                Activity::create([
                    'application_id' => $app->id,
                    'from_status' => 'offer',
                    'to_status' => 'accepted',
                    'created_at' => now()->subDays(2),
                ]);
            }

            // Create Interviews for interview / offer / phone_screen apps
            if ($data['status'] === 'interview') {
                Interview::create([
                    'application_id' => $app->id,
                    'round_type' => 'technical',
                    'scheduled_at' => now()->addDays(2),
                    'outcome' => 'pending',
                    'prep_notes' => 'Technical coding & system design round.',
                ]);
            } else if ($data['status'] === 'offer') {
                Interview::create([
                    'application_id' => $app->id,
                    'round_type' => 'final',
                    'scheduled_at' => now()->subDays(5),
                    'outcome' => 'passed',
                    'prep_notes' => 'Final leadership interview passed.',
                ]);
            }
        }

        // 4. Create Unread Notifications for Demo User
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
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        DatabaseNotification::create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\FollowUpReminderNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'next_action',
                'title' => 'Follow-up Due Today',
                'message' => 'Time to follow up on your Lead Software Engineer application at Meta.',
            ],
            'read_at' => null,
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);
    }
}
