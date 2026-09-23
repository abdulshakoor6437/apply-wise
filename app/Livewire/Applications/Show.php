<?php

namespace App\Livewire\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Activity;
use App\Models\Application;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Interview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Application $application;

    // Application edit slide-over state
    public bool $showSlideOver = false;

    // Application Form fields
    public string $jobTitle = '';
    public string $companyId = '';
    public string $status = 'wishlist';
    public ?string $dateApplied = null;
    public ?string $source = null;
    public ?string $jobPostingUrl = null;
    public ?string $salaryMin = null;
    public ?string $salaryMax = null;
    public ?int $excitementRating = null;
    public ?string $nextActionDate = null;
    public ?string $notes = null;
    public array $documentIds = [];

    // Document linking modal state
    public bool $showAttachDocumentModal = false;

    // Interview CRUD state
    public bool $showInterviewSlideOver = false;
    public bool $showDeleteInterviewModal = false;
    public ?string $editingInterviewId = null;
    public ?string $deletingInterviewId = null;

    // Interview Form fields
    public string $interviewRoundType = 'phone_screen';
    public string $interviewScheduledAt = '';
    public ?string $interviewFormat = null;
    public ?string $interviewPrepNotes = null;
    public string $interviewOutcome = 'pending';

    // Manual Activity Note state
    public string $newActivityNote = '';

    protected function rules(): array
    {
        return [
            'jobTitle'         => 'required|string|max:255',
            'companyId'        => 'required|exists:companies,id',
            'status'           => 'required',
            'dateApplied'      => 'nullable|date',
            'source'           => 'nullable|string|max:100',
            'jobPostingUrl'    => 'nullable|url|max:2048',
            'salaryMin'        => 'nullable|numeric|min:0',
            'salaryMax'        => 'nullable|numeric|min:0|gte:salaryMin',
            'excitementRating' => 'nullable|integer|min:1|max:5',
            'nextActionDate'   => 'nullable|date',
            'notes'            => 'nullable|string',
            'documentIds'      => 'nullable|array',
            'documentIds.*'    => 'exists:documents,id',
        ];
    }

    protected function interviewRules(): array
    {
        return [
            'interviewRoundType'  => 'required|string|in:phone_screen,technical,behavioral,onsite,final,other',
            'interviewScheduledAt'=> 'required|date',
            'interviewFormat'     => 'nullable|string|in:remote,onsite,hybrid',
            'interviewPrepNotes'  => 'nullable|string',
            'interviewOutcome'    => 'required|string|in:pending,passed,failed,cancelled',
        ];
    }

    public function mount(Application $application): void
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $this->application = $application->load(['company', 'activities', 'interviews', 'documents']);
    }

    public function updatedStatus($value): void
    {
        if (in_array($value, ['applied', 'phone_screen', 'interview', 'offer', 'accepted', 'rejected']) && empty($this->dateApplied)) {
            $this->dateApplied = now()->format('Y-m-d');
        }
    }

    public function openEdit(): void
    {
        $this->jobTitle         = $this->application->job_title;
        $this->companyId        = $this->application->company_id;
        $this->status           = $this->application->status instanceof ApplicationStatus ? $this->application->status->value : $this->application->status;
        $this->dateApplied      = $this->application->date_applied?->format('Y-m-d');
        $this->source           = $this->application->source;
        $this->jobPostingUrl    = $this->application->job_posting_url;
        $this->salaryMin        = $this->application->salary_min ? (string)$this->application->salary_min : null;
        $this->salaryMax        = $this->application->salary_max ? (string)$this->application->salary_max : null;
        $this->excitementRating = $this->application->excitement_rating;
        $this->nextActionDate   = $this->application->next_action_date?->format('Y-m-d');
        $this->notes            = $this->application->notes;
        $this->documentIds      = $this->application->documents->pluck('id')->toArray();

        $this->showSlideOver = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $this->application->update([
            'job_title'         => $validated['jobTitle'],
            'company_id'        => $validated['companyId'],
            'status'            => $validated['status'],
            'date_applied'      => $validated['dateApplied'] ?: null,
            'source'            => $validated['source'] ?: null,
            'job_posting_url'   => $validated['jobPostingUrl'] ?: null,
            'salary_min'        => $validated['salaryMin'] ?: null,
            'salary_max'        => $validated['salaryMax'] ?: null,
            'excitement_rating' => $validated['excitementRating'] ?: null,
            'next_action_date'  => $validated['nextActionDate'] ?: null,
            'notes'             => $validated['notes'] ?: null,
        ]);

        if (isset($validated['documentIds'])) {
            $this->application->documents()->sync($validated['documentIds']);
        }

        $this->application->refresh();
        $this->application->load(['company', 'activities', 'interviews', 'documents']);

        $this->showSlideOver = false;
        $this->dispatch('toast', type: 'success', message: 'Application updated successfully.');
    }

    // ==================== DOCUMENT LINKING METHODS (Part B1) ====================

    public function openAttachDocument(): void
    {
        $this->showAttachDocumentModal = true;
    }

    public function attachDocument(string $documentId): void
    {
        $doc = Document::where('user_id', Auth::id())->findOrFail($documentId);

        $this->application->documents()->syncWithoutDetaching([$doc->id]);
        $this->application->load(['company', 'activities', 'interviews', 'documents']);

        $this->showAttachDocumentModal = false;
        $this->dispatch('toast', type: 'success', message: "Linked '{$doc->label}' to application.");
    }

    public function unlinkDocument(string $documentId): void
    {
        $doc = Document::where('user_id', Auth::id())->find($documentId);

        $this->application->documents()->detach($documentId);
        $this->application->load(['company', 'activities', 'interviews', 'documents']);

        $label = $doc ? $doc->label : 'Document';
        $this->dispatch('toast', type: 'info', message: "Unlinked '{$label}'.");
    }

    // ==================== INTERVIEW CRUD METHODS ====================

    public function openCreateInterview(): void
    {
        $this->resetValidation();
        $this->editingInterviewId = null;
        $this->interviewRoundType = 'phone_screen';
        $this->interviewScheduledAt = now()->addDay()->format('Y-m-d\TH:i');
        $this->interviewFormat = 'remote';
        $this->interviewPrepNotes = null;
        $this->interviewOutcome = 'pending';

        $this->showInterviewSlideOver = true;
    }

    public function openEditInterview(string $id): void
    {
        $this->resetValidation();
        $interview = Interview::where('application_id', $this->application->id)->findOrFail($id);

        $this->editingInterviewId  = $interview->id;
        $this->interviewRoundType  = $interview->round_type;
        $this->interviewScheduledAt= $interview->scheduled_at ? $interview->scheduled_at->format('Y-m-d\TH:i') : '';
        $this->interviewFormat     = $interview->format;
        $this->interviewPrepNotes  = $interview->prep_notes;
        $this->interviewOutcome    = $interview->outcome ?? 'pending';

        $this->showInterviewSlideOver = true;
    }

    public function saveInterview(): void
    {
        $validated = $this->validate($this->interviewRules());

        $data = [
            'application_id' => $this->application->id,
            'round_type'     => $validated['interviewRoundType'],
            'scheduled_at'   => $validated['interviewScheduledAt'],
            'format'         => $validated['interviewFormat'] ?: null,
            'prep_notes'     => $validated['interviewPrepNotes'] ?: null,
            'outcome'        => $validated['interviewOutcome'],
        ];

        if ($this->editingInterviewId) {
            $interview = Interview::where('application_id', $this->application->id)->findOrFail($this->editingInterviewId);
            $interview->update($data);
            $this->dispatch('toast', type: 'success', message: 'Interview updated successfully.');
        } else {
            Interview::create($data);
            $this->dispatch('toast', type: 'success', message: 'Interview scheduled successfully.');
        }

        $this->showInterviewSlideOver = false;
        $this->application->load(['company', 'activities', 'interviews', 'documents']);
    }

    public function confirmDeleteInterview(string $id): void
    {
        $this->deletingInterviewId = $id;
        $this->showDeleteInterviewModal = true;
    }

    public function deleteInterview(): void
    {
        if ($this->deletingInterviewId) {
            $interview = Interview::where('application_id', $this->application->id)->find($this->deletingInterviewId);
            if ($interview) {
                $interview->delete();
                $this->dispatch('toast', type: 'info', message: 'Interview deleted.');
            }
        }

        $this->showDeleteInterviewModal = false;
        $this->deletingInterviewId = null;
        $this->application->load(['company', 'activities', 'interviews', 'documents']);
    }

    public function getDeletingInterviewProperty(): ?Interview
    {
        return $this->deletingInterviewId
            ? Interview::find($this->deletingInterviewId)
            : null;
    }

    // ==================== MANUAL ACTIVITY NOTE METHOD ====================

    public function addActivityNote(): void
    {
        $this->validate([
            'newActivityNote' => 'required|string|max:1000',
        ], [
            'newActivityNote.required' => 'Please enter a note before adding.',
        ]);

        Activity::create([
            'application_id' => $this->application->id,
            'from_status'    => null,
            'to_status'      => null,
            'notes'          => trim($this->newActivityNote),
        ]);

        $this->newActivityNote = '';
        $this->application->load(['company', 'activities', 'interviews', 'documents']);
        $this->dispatch('toast', type: 'success', message: 'Note added to activity timeline.');
    }

    public function render()
    {
        $companies = Company::where('user_id', Auth::id())->orderBy('name')->get();
        $contacts = Contact::where('company_id', $this->application->company_id)
            ->where('user_id', Auth::id())
            ->get();
        $allUserDocuments = Document::where('user_id', Auth::id())->orderBy('label')->get();

        return view('livewire.pages.applications.show', [
            'companies'        => $companies,
            'contacts'         => $contacts,
            'allUserDocuments' => $allUserDocuments,
        ])->layout('layouts.app', ['title' => $this->application->job_title . ' — Details']);
    }
}
