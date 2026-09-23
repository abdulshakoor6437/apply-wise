<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_documents_page_renders_for_authenticated_users(): void
    {
        $response = $this->actingAs($this->user)->get(route('documents'));

        $response->assertOk();
        $response->assertSee('Documents');
        $response->assertSee('Upload Document');
        $response->assertSee('Resumes');
        $response->assertSee('Cover Letters');
    }

    public function test_user_can_upload_resume_document(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('test-resume.pdf', 1024, 'application/pdf');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Documents\Index::class)
            ->set('label', 'Resume v1 — Full Stack')
            ->set('docType', 'resume')
            ->set('file', $file)
            ->call('saveUpload')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('documents', [
            'user_id'           => $this->user->id,
            'label'             => 'Resume v1 — Full Stack',
            'doc_type'          => 'resume',
            'original_filename' => 'test-resume.pdf',
        ]);

        $doc = Document::where('user_id', $this->user->id)->first();
        Storage::disk('local')->assertExists($doc->file_path);
    }

    public function test_user_can_edit_document_label(): void
    {
        $doc = Document::create([
            'user_id'           => $this->user->id,
            'label'             => 'Original Label',
            'doc_type'          => 'resume',
            'file_path'         => 'documents/' . $this->user->id . '/test.pdf',
            'original_filename' => 'test.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Documents\Index::class)
            ->call('openEdit', $doc->id)
            ->set('editLabel', 'Updated Resume Label')
            ->call('saveEdit')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('documents', [
            'id'    => $doc->id,
            'label' => 'Updated Resume Label',
        ]);
    }

    public function test_user_can_delete_document_and_file_is_removed(): void
    {
        Storage::fake('local');
        $path = 'documents/' . $this->user->id . '/test.pdf';
        Storage::put($path, 'dummy content');

        $doc = Document::create([
            'user_id'           => $this->user->id,
            'label'             => 'Resume to Delete',
            'doc_type'          => 'resume',
            'file_path'         => $path,
            'original_filename' => 'test.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Documents\Index::class)
            ->call('confirmDelete', $doc->id)
            ->call('delete')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        Storage::assertMissing($path);
    }

    public function test_user_can_download_own_document(): void
    {
        Storage::fake('local');
        $path = 'documents/' . $this->user->id . '/resume.pdf';
        Storage::put($path, 'resume content');

        $doc = Document::create([
            'user_id'           => $this->user->id,
            'label'             => 'My Resume',
            'doc_type'          => 'resume',
            'file_path'         => $path,
            'original_filename' => 'resume.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.download', $doc->id));
        $response->assertOk();
    }

    public function test_user_cannot_download_another_users_document(): void
    {
        Storage::fake('local');
        $otherUser = User::factory()->create();
        $path = 'documents/' . $otherUser->id . '/secret.pdf';
        Storage::put($path, 'secret content');

        $doc = Document::create([
            'user_id'           => $otherUser->id,
            'label'             => 'Other User Document',
            'doc_type'          => 'resume',
            'file_path'         => $path,
            'original_filename' => 'secret.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        $response = $this->actingAs($this->user)->get(route('documents.download', $doc->id));
        $response->assertForbidden();
    }

    public function test_response_rate_calculation(): void
    {
        $doc = Document::create([
            'user_id'           => $this->user->id,
            'label'             => 'Resume v2',
            'doc_type'          => 'resume',
            'file_path'         => 'documents/' . $this->user->id . '/test.pdf',
            'original_filename' => 'test.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        // Edge case: 0 applications -> null
        $this->assertNull($doc->response_rate);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name'    => 'Acme Corp',
        ]);

        // App 1: applied (not counted as response)
        $app1 = Application::create([
            'user_id'    => $this->user->id,
            'company_id' => $company->id,
            'job_title'  => 'Dev 1',
            'status'     => ApplicationStatus::APPLIED,
            'priority'   => 0,
        ]);

        // App 2: interview (counted as response)
        $app2 = Application::create([
            'user_id'    => $this->user->id,
            'company_id' => $company->id,
            'job_title'  => 'Dev 2',
            'status'     => ApplicationStatus::INTERVIEW,
            'priority'   => 1,
        ]);

        // App 3: rejected (not advanced response)
        $app3 = Application::create([
            'user_id'    => $this->user->id,
            'company_id' => $company->id,
            'job_title'  => 'Dev 3',
            'status'     => ApplicationStatus::REJECTED,
            'priority'   => 2,
        ]);

        $doc->applications()->attach([$app1->id, $app2->id, $app3->id]);
        $doc->refresh();

        // 1 out of 3 responded -> 33.3%
        $this->assertEquals(33.3, $doc->response_rate);
    }

    public function test_attach_and_unlink_document_on_application(): void
    {
        $company = Company::create([
            'user_id' => $this->user->id,
            'name'    => 'Tech Start',
        ]);

        $app = Application::create([
            'user_id'    => $this->user->id,
            'company_id' => $company->id,
            'job_title'  => 'Software Engineer',
            'status'     => ApplicationStatus::APPLIED,
            'priority'   => 0,
        ]);

        $doc = Document::create([
            'user_id'           => $this->user->id,
            'label'             => 'Resume v1',
            'doc_type'          => 'resume',
            'file_path'         => 'documents/' . $this->user->id . '/test.pdf',
            'original_filename' => 'test.pdf',
            'file_size'         => 1024,
            'mime_type'         => 'application/pdf',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Applications\Show::class, ['application' => $app])
            ->call('attachDocument', $doc->id)
            ->assertDispatched('toast');

        $this->assertTrue($app->fresh()->documents->contains($doc->id));

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Applications\Show::class, ['application' => $app])
            ->call('unlinkDocument', $doc->id)
            ->assertDispatched('toast');

        $this->assertFalse($app->fresh()->documents->contains($doc->id));
        // Ensure document record was NOT deleted
        $this->assertDatabaseHas('documents', ['id' => $doc->id]);
    }
}
