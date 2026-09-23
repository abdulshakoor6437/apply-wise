<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public string $activeTab = 'resume'; // 'resume' or 'cover_letter'

    // Modal & Slide-over states
    public bool $showUploadSlideOver = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?string $editingId = null;
    public ?string $deletingId = null;
    public ?string $expandedDocId = null;

    // Upload form fields
    public string $label = '';
    public string $docType = 'resume';
    public $file;

    // Edit form fields
    public string $editLabel = '';

    public function mount(): void
    {
        if (request()->query('upload') == '1') {
            $this->openUpload();
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['resume', 'cover_letter'])) {
            $this->activeTab = $tab;
        }
    }

    public function openUpload(): void
    {
        $this->resetValidation();
        $this->label = '';
        $this->docType = 'resume';
        $this->file = null;
        $this->showUploadSlideOver = true;
    }

    public function saveUpload(): void
    {
        $validated = $this->validate([
            'label'   => 'required|string|max:255',
            'docType' => 'required|in:resume,cover_letter',
            'file'    => 'required|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'file.max' => 'Document file must not be larger than 5MB.',
            'file.mimes' => 'Document must be a PDF, DOC, or DOCX file.',
        ]);

        $path = $this->file->store('documents/' . Auth::id(), 'local');

        Document::create([
            'user_id'           => Auth::id(),
            'label'             => $validated['label'],
            'doc_type'          => $validated['docType'],
            'file_path'         => $path,
            'original_filename' => $this->file->getClientOriginalName(),
            'file_size'         => $this->file->getSize(),
            'mime_type'         => $this->file->getClientMimeType(),
        ]);

        $this->showUploadSlideOver = false;
        $this->reset(['label', 'docType', 'file']);
        $this->dispatch('toast', type: 'success', message: 'Document uploaded successfully.');
    }

    public function openEdit(string $id): void
    {
        $this->resetValidation();
        $doc = Document::where('user_id', Auth::id())->findOrFail($id);

        $this->editingId = $doc->id;
        $this->editLabel = $doc->label;
        $this->showEditModal = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editLabel' => 'required|string|max:255',
        ]);

        if ($this->editingId) {
            $doc = Document::where('user_id', Auth::id())->findOrFail($this->editingId);
            $doc->update([
                'label' => $this->editLabel,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Document updated successfully.');
        }

        $this->showEditModal = false;
        $this->editingId = null;
    }

    public function confirmDelete(string $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $doc = Document::where('user_id', Auth::id())->find($this->deletingId);
            if ($doc) {
                if (Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                }
                $doc->delete();
                $this->dispatch('toast', type: 'info', message: 'Document deleted.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleExpandedDoc(string $id): void
    {
        $this->expandedDocId = $this->expandedDocId === $id ? null : $id;
    }

    public function getDeletingDocumentProperty(): ?Document
    {
        return $this->deletingId
            ? Document::withCount('applications')->find($this->deletingId)
            : null;
    }

    public function render()
    {
        $documents = Document::where('user_id', Auth::id())
            ->where('doc_type', $this->activeTab)
            ->with(['applications.company'])
            ->orderBy('created_at', 'desc')
            ->get();

        $resumeCount = Document::where('user_id', Auth::id())->where('doc_type', 'resume')->count();
        $coverLetterCount = Document::where('user_id', Auth::id())->where('doc_type', 'cover_letter')->count();

        return view('livewire.pages.documents.index', [
            'documents'        => $documents,
            'resumeCount'      => $resumeCount,
            'coverLetterCount' => $coverLetterCount,
        ])->layout('layouts.app', ['title' => 'Document Library & Resume Versioning']);
    }
}
