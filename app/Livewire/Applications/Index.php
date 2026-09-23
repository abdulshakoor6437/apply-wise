<?php

namespace App\Livewire\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // View & Filter properties
    public string $viewMode = 'board'; // 'board' or 'list'
    public string $search = '';
    public string $filterCompany = '';
    public string $filterSource = '';

    // Modal & Slide-over states
    public bool $showSlideOver = false;
    public bool $showDeleteModal = false;
    public ?string $editingId = null;
    public ?string $deletingId = null;

    // Form fields
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

    protected $queryString = [
        'viewMode'      => ['except' => 'board', 'as' => 'view'],
        'search'        => ['except' => ''],
        'filterCompany' => ['except' => ''],
        'filterSource'  => ['except' => ''],
    ];

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

    public function mount(): void
    {
        if (request()->query('create') == '1') {
            $this->openCreate();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCompany(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSource(): void
    {
        $this->resetPage();
    }

    public function updatedStatus($value): void
    {
        if (in_array($value, ['applied', 'phone_screen', 'interview', 'offer', 'accepted', 'rejected']) && empty($this->dateApplied)) {
            $this->dateApplied = now()->format('Y-m-d');
        }
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingId = null;
        $this->showSlideOver = true;
    }

    public function openEdit(string $id): void
    {
        $this->resetValidation();
        $app = Application::where('user_id', Auth::id())->with('documents')->findOrFail($id);

        $this->editingId        = $app->id;
        $this->jobTitle         = $app->job_title;
        $this->companyId        = $app->company_id;
        $this->status           = $app->status instanceof ApplicationStatus ? $app->status->value : $app->status;
        $this->dateApplied      = $app->date_applied?->format('Y-m-d');
        $this->source           = $app->source;
        $this->jobPostingUrl    = $app->job_posting_url;
        $this->salaryMin        = $app->salary_min ? (string)$app->salary_min : null;
        $this->salaryMax        = $app->salary_max ? (string)$app->salary_max : null;
        $this->excitementRating = $app->excitement_rating;
        $this->nextActionDate   = $app->next_action_date?->format('Y-m-d');
        $this->notes            = $app->notes;
        $this->documentIds      = $app->documents->pluck('id')->toArray();

        $this->showSlideOver = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
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
        ];

        if ($this->editingId) {
            $app = Application::where('user_id', Auth::id())->findOrFail($this->editingId);
            $app->update($data);
            if (isset($validated['documentIds'])) {
                $app->documents()->sync($validated['documentIds']);
            }
            $this->dispatch('toast', type: 'success', message: 'Application updated successfully.');
        } else {
            $data['user_id']  = Auth::id();
            $data['priority'] = 0;
            $app = Application::create($data);
            if (!empty($validated['documentIds'])) {
                $app->documents()->sync($validated['documentIds']);
            }
            $this->dispatch('toast', type: 'success', message: 'Application created successfully.');
        }

        $this->showSlideOver = false;
        $this->resetForm();
    }

    public function confirmDelete(string $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $app = Application::where('user_id', Auth::id())->find($this->deletingId);
            if ($app) {
                $app->delete();
                $this->dispatch('toast', type: 'info', message: 'Application deleted.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function updateCardPosition(string $applicationId, string $newStatus, int $newIndex): void
    {
        $app = Application::where('user_id', Auth::id())->find($applicationId);

        if (! $app) {
            return;
        }

        $app->status   = $newStatus;
        $app->priority = $newIndex;

        if (in_array($newStatus, ['applied', 'phone_screen', 'interview', 'offer', 'accepted', 'rejected']) && is_null($app->date_applied)) {
            $app->date_applied = now();
        }

        $app->save();
    }

    private function resetForm(): void
    {
        $this->editingId        = null;
        $this->jobTitle         = '';
        $this->companyId        = '';
        $this->status           = 'wishlist';
        $this->dateApplied      = null;
        $this->source           = null;
        $this->jobPostingUrl    = null;
        $this->salaryMin        = null;
        $this->salaryMax        = null;
        $this->excitementRating = null;
        $this->nextActionDate   = null;
        $this->notes            = null;
        $this->documentIds      = [];
    }

    public function getDeletingApplicationProperty(): ?Application
    {
        return $this->deletingId
            ? Application::with('company')->find($this->deletingId)
            : null;
    }

    public function render()
    {
        $userCompanies = Company::where('user_id', Auth::id())->orderBy('name')->get();
        $allUserDocuments = Document::where('user_id', Auth::id())->orderBy('label')->get();

        // Base query
        $query = Application::with(['company', 'interviews', 'documents'])
            ->where('user_id', Auth::id())
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('job_title', 'like', $term)
                        ->orWhereHas('company', function ($c) use ($term) {
                            $c->where('name', 'like', $term);
                        });
                });
            })
            ->when($this->filterCompany, function ($q) {
                $q->where('company_id', $this->filterCompany);
            })
            ->when($this->filterSource, function ($q) {
                $q->where('source', $this->filterSource);
            });

        if ($this->viewMode === 'list') {
            $applications = (clone $query)->orderBy('created_at', 'desc')->paginate(10);
            $boardData = [];
        } else {
            $allApps = (clone $query)->orderBy('priority', 'asc')->orderBy('created_at', 'desc')->get();
            $boardData = [];
            foreach (ApplicationStatus::kanbanStages() as $stage) {
                $boardData[$stage->value] = $allApps->filter(function ($app) use ($stage) {
                    $appStatus = $app->status instanceof ApplicationStatus ? $app->status->value : $app->status;
                    return $appStatus === $stage->value;
                })->values();
            }
            $applications = collect();
        }

        return view('livewire.pages.applications.index', [
            'companies'        => $userCompanies,
            'allUserDocuments' => $allUserDocuments,
            'boardData'        => $boardData,
            'applications'     => $applications,
            'stages'           => ApplicationStatus::kanbanStages(),
        ])->layout('layouts.app', ['title' => 'Applications & Kanban Board']);
    }
}
