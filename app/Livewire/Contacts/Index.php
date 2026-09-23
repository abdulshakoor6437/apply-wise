<?php

namespace App\Livewire\Contacts;

use App\Models\Company;
use App\Models\Contact;
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

    // Filters
    public string $filterCompany = '';
    public string $filterRole    = '';

    // UI state
    public bool $showSlideOver   = false;
    public bool $showDeleteModal = false;

    // IDs
    public ?string $editingId    = null;
    public ?string $deletingId   = null;
    public string  $deletingName = '';

    // Form fields
    public string $name              = '';
    public string $role              = '';
    public string $company_id        = '';
    public string $email             = '';
    public string $phone             = '';
    public string $linkedin_url      = '';
    public string $last_contacted_at = '';
    public string $notes             = '';

    /** Companies list for the dropdown (loaded once in mount). */
    public array $companies = [];

    protected array $sortable = ['name', 'role', 'last_contacted_at', 'created_at'];

    public static array $roleOptions = [
        'recruiter'      => 'Recruiter',
        'hiring_manager' => 'Hiring Manager',
        'engineer'       => 'Engineer',
        'referral'       => 'Referral',
        'other'          => 'Other',
    ];

    protected function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'role'              => 'required|in:recruiter,hiring_manager,engineer,referral,other',
            'company_id'        => 'required|exists:companies,id',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'linkedin_url'      => 'nullable|url|max:500',
            'last_contacted_at' => 'nullable|date',
            'notes'             => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        // Load user's companies for filter + form dropdown
        $this->loadCompanies();
    }

    private function loadCompanies(): void
    {
        $this->companies = auth()->user()
            ->companies()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterCompany(): void { $this->resetPage(); }
    public function updatedFilterRole(): void    { $this->resetPage(); }

    public function render(): View
    {
        $sortBy  = in_array($this->sortBy, $this->sortable) ? $this->sortBy : 'name';
        $sortDir = $this->sortDir === 'desc' ? 'desc' : 'asc';

        $contacts = auth()->user()
            ->contacts()
            ->with('company')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCompany, fn ($q) => $q->where('company_id', $this->filterCompany))
            ->when($this->filterRole,    fn ($q) => $q->where('role', $this->filterRole))
            ->orderBy($sortBy, $sortDir)
            ->paginate(10);

        return view('livewire.pages.contacts.index', [
            'contacts'    => $contacts,
            'roleOptions' => static::$roleOptions,
        ])->layout('layouts.app', ['title' => 'Contacts']);
    }

    public function sort(string $column): void
    {
        if (!in_array($column, $this->sortable)) return;

        $this->sortBy  = $this->sortBy === $column
            ? $this->sortBy
            : $column;
        $this->sortDir = ($this->sortBy === $column && $this->sortDir === 'asc') ? 'desc' : 'asc';
        $this->sortBy  = $column;
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId     = null;
        $this->showSlideOver = true;
    }

    public function openEdit(string $id): void
    {
        $contact = auth()->user()->contacts()->findOrFail($id);

        $this->editingId         = $id;
        $this->name              = $contact->name;
        $this->role              = $contact->role              ?? '';
        $this->company_id        = $contact->company_id        ?? '';
        $this->email             = $contact->email             ?? '';
        $this->phone             = $contact->phone             ?? '';
        $this->linkedin_url      = $contact->linkedin_url      ?? '';
        $this->last_contacted_at = $contact->last_contacted_at
            ? $contact->last_contacted_at->format('Y-m-d') : '';
        $this->notes             = $contact->notes             ?? '';
        $this->showSlideOver     = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'              => $this->name,
            'role'              => $this->role              ?: null,
            'company_id'        => $this->company_id        ?: null,
            'email'             => $this->email             ?: null,
            'phone'             => $this->phone             ?: null,
            'linkedin_url'      => $this->linkedin_url      ?: null,
            'last_contacted_at' => $this->last_contacted_at ?: null,
            'notes'             => $this->notes             ?: null,
        ];

        if ($this->editingId) {
            auth()->user()->contacts()->findOrFail($this->editingId)->update($data);
            $message = 'Contact updated successfully.';
        } else {
            auth()->user()->contacts()->create($data);
            $message = 'Contact added successfully.';
        }

        $this->showSlideOver = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function confirmDelete(string $id): void
    {
        $contact = auth()->user()->contacts()->find($id);
        if (!$contact) return;

        $this->deletingId      = $id;
        $this->deletingName    = $contact->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->deletingId) return;

        $contact = auth()->user()->contacts()->find($this->deletingId);
        if ($contact) $contact->delete();

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->deletingName    = '';
        $this->dispatch('toast', type: 'success', message: 'Contact deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->name              = '';
        $this->role              = '';
        $this->company_id        = '';
        $this->email             = '';
        $this->phone             = '';
        $this->linkedin_url      = '';
        $this->last_contacted_at = '';
        $this->notes             = '';
        $this->resetValidation();
    }
}
