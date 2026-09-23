<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Search & sort
    public string $search  = '';
    public string $sortBy  = 'name';
    public string $sortDir = 'asc';

    // UI state
    public bool $showSlideOver   = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal   = false;

    // View & Form
    public ?Company $viewingCompany = null;
    public ?string $editingId   = null;
    public ?string $deletingId  = null;
    public string  $deletingName = '';

    // Form fields
    public string $name         = '';
    public string $industry     = '';
    public string $location     = '';
    public string $website      = '';
    public string $linkedin_url = '';
    public string $size         = '';
    public string $notes        = '';

    /** Allowed sortable columns (whitelist against SQL injection). */
    protected array $sortable = ['name', 'industry', 'location', 'created_at'];

    /** Company size options: stored key => displayed label. */
    public static array $sizeOptions = [
        'startup'    => 'Startup',
        'small'      => 'Small (1–50)',
        'medium'     => 'Medium (51–200)',
        'large'      => 'Large (201–1,000)',
        'enterprise' => 'Enterprise (1,000+)',
    ];

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'industry'     => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'size'         => 'nullable|in:startup,small,medium,large,enterprise',
            'notes'        => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        // Auto-open create panel when navigated from Dashboard "Add Company"
        if (request()->has('create')) {
            $this->openCreate();
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $sortBy = in_array($this->sortBy, $this->sortable) ? $this->sortBy : 'name';
        $sortDir = $this->sortDir === 'desc' ? 'desc' : 'asc';

        $companies = auth()->user()
            ->companies()
            ->withCount(['applications', 'contacts'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('industry', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(10);

        return view('livewire.pages.companies.index', [
            'companies'   => $companies,
            'sizeOptions' => static::$sizeOptions,
        ])->layout('layouts.app', ['title' => 'Companies']);
    }

    public function sort(string $column): void
    {
        if (!in_array($column, $this->sortable)) return;

        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openView(string $id): void
    {
        $this->viewingCompany = auth()->user()
            ->companies()
            ->with(['applications', 'contacts'])
            ->find($id);

        if ($this->viewingCompany) {
            $this->showViewModal = true;
        }
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingCompany = null;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId     = null;
        $this->showSlideOver = true;
    }

    public function openEdit(string $id): void
    {
        $company = auth()->user()->companies()->findOrFail($id);

        $this->editingId     = $id;
        $this->name          = $company->name;
        $this->industry      = $company->industry    ?? '';
        $this->location      = $company->location    ?? '';
        $this->website       = $company->website     ?? '';
        $this->linkedin_url  = $company->linkedin_url ?? '';
        $this->size          = $company->size        ?? '';
        $this->notes         = $company->notes       ?? '';
        $this->showSlideOver = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'         => $this->name,
            'industry'     => $this->industry     ?: null,
            'location'     => $this->location     ?: null,
            'website'      => $this->website      ?: null,
            'linkedin_url' => $this->linkedin_url ?: null,
            'size'         => $this->size         ?: null,
            'notes'        => $this->notes        ?: null,
        ];

        if ($this->editingId) {
            auth()->user()->companies()->findOrFail($this->editingId)->update($data);
            $message = 'Company updated successfully.';
        } else {
            auth()->user()->companies()->create($data);
            $message = 'Company added successfully.';
        }

        $this->showSlideOver = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function confirmDelete(string $id): void
    {
        $company = auth()->user()->companies()->find($id);
        if (!$company) return;

        $this->deletingId    = $id;
        $this->deletingName  = $company->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->deletingId) return;

        $company = auth()->user()->companies()->find($this->deletingId);
        if ($company) {
            $company->delete();
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->deletingName    = '';
        $this->dispatch('toast', type: 'success', message: 'Company deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->name         = '';
        $this->industry     = '';
        $this->location     = '';
        $this->website      = '';
        $this->linkedin_url = '';
        $this->size         = '';
        $this->notes        = '';
        $this->resetValidation();
    }
}
