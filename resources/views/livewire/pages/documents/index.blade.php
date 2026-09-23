<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 tracking-tight">Documents</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage your resumes and cover letters with real-time response rate metrics.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="openUpload"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                id="upload-document-btn"
            >
                <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                Upload Document
            </button>
        </div>
    </div>

    {{-- Tabs (Resumes vs Cover Letters) --}}
    <div class="border-b border-gray-200 dark:border-slate-800">
        <nav class="-mb-px flex gap-8">
            <button
                type="button"
                wire:click="setTab('resume')"
                class="py-3 px-1 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 {{ $activeTab === 'resume' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300' }}"
                id="tab-resumes"
            >
                <x-heroicon-o-document-text class="w-4 h-4" />
                <span>Resumes</span>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'resume' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400' }}">
                    {{ $resumeCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('cover_letter')"
                class="py-3 px-1 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 {{ $activeTab === 'cover_letter' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 hover:border-gray-300' }}"
                id="tab-cover-letters"
            >
                <x-heroicon-o-document class="w-4 h-4" />
                <span>Cover Letters</span>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'cover_letter' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400' }}">
                    {{ $coverLetterCount }}
                </span>
            </button>
        </nav>
    </div>

    {{-- Documents Grid --}}
    @if($documents->isEmpty())
        <div class="flex items-center justify-center min-h-[320px] rounded-2xl border-2 border-dashed border-gray-200 bg-white p-8 text-center">
            <div class="max-w-md">
                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-indigo-50 text-indigo-600">
                    @if($activeTab === 'resume')
                        <x-heroicon-o-document-text class="w-7 h-7" />
                    @else
                        <x-heroicon-o-document class="w-7 h-7" />
                    @endif
                </div>
                <h3 class="text-base font-bold text-gray-900">
                    No {{ $activeTab === 'resume' ? 'resumes' : 'cover letters' }} uploaded yet
                </h3>
                <p class="mt-1.5 text-xs text-gray-500 leading-relaxed">
                    Upload your first {{ $activeTab === 'resume' ? 'resume version' : 'cover letter' }} to start tracking response rates across your job applications.
                </p>
                <button
                    type="button"
                    wire:click="openUpload"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-all shadow-sm"
                >
                    <x-heroicon-o-plus class="w-4 h-4" />
                    Upload Your First Document
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($documents as $doc)
                @php
                    $isResume = $doc->doc_type === 'resume';
                    $iconBg = $isResume ? 'bg-indigo-50 text-indigo-600' : 'bg-teal-50 text-teal-600';
                    $fileSizeFormatted = $doc->file_size ? ($doc->file_size > 1048576 ? round($doc->file_size / 1048576, 1) . ' MB' : round($doc->file_size / 1024, 0) . ' KB') : 'Unknown size';
                    
                    $totalApps = $doc->applications->count();
                    $respondedApps = $doc->applications->filter(function($app) {
                        $s = $app->status instanceof \App\Enums\ApplicationStatus ? $app->status->value : $app->status;
                        return in_array($s, ['phone_screen', 'interview', 'offer', 'accepted']);
                    })->count();

                    $rate = $doc->response_rate;
                    $rateColor = match(true) {
                        $rate === null => 'bg-gray-200',
                        $rate >= 30.0 => 'bg-emerald-500',
                        $rate >= 15.0 => 'bg-amber-500',
                        default => 'bg-rose-500',
                    };

                    $rateBadge = match(true) {
                        $rate === null => 'bg-gray-100 text-gray-600',
                        $rate >= 30.0 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        $rate >= 15.0 => 'bg-amber-50 text-amber-700 border-amber-200',
                        default => 'bg-rose-50 text-rose-700 border-rose-200',
                    };
                @endphp

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                    {{-- Header Row --}}
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0">
                                    @if($isResume)
                                        <x-heroicon-o-document-text class="w-5 h-5" />
                                    @else
                                        <x-heroicon-o-document class="w-5 h-5" />
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 leading-snug tracking-tight hover:text-indigo-600 transition-colors">
                                        {{ $doc->label }}
                                    </h3>
                                    <p class="text-[11px] text-gray-400 mt-0.5 truncate max-w-[200px]" title="{{ $doc->original_filename }}">
                                        {{ $doc->original_filename }}
                                    </p>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded {{ $isResume ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">
                                {{ $isResume ? 'Resume' : 'Cover Letter' }}
                            </span>
                        </div>

                        {{-- Metadata Line --}}
                        <div class="flex items-center gap-3 text-xs text-gray-400 mt-3 pt-3 border-t border-gray-50">
                            <span>{{ $fileSizeFormatted }}</span>
                            <span>•</span>
                            <span>Uploaded {{ $doc->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    {{-- Response Rate Section --}}
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            @if($rate !== null)
                                <span class="font-semibold text-gray-700">
                                    Response Rate: <span class="text-indigo-600 font-bold">{{ $rate }}%</span>
                                    <span class="text-gray-500 font-normal">({{ $respondedApps }} of {{ $totalApps }} {{ Str::plural('application', $totalApps) }})</span>
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $rateBadge }}">
                                    {{ $rate }}%
                                </span>
                            @else
                                <span class="text-xs text-gray-500 font-medium">
                                    Not linked to any applications yet
                                </span>
                            @endif
                        </div>

                        @if($rate !== null)
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="{{ $rateColor }} h-2 rounded-full transition-all duration-300" style="width: {{ max($rate, 5) }}%"></div>
                            </div>
                        @else
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gray-300 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        @endif
                    </div>

                    {{-- Linked Applications Dropdown Toggle --}}
                    <div>
                        <button
                            type="button"
                            wire:click="toggleExpandedDoc('{{ $doc->id }}')"
                            class="w-full text-left flex items-center justify-between px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-xs font-medium text-gray-700 transition-colors"
                        >
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-link class="w-3.5 h-3.5 text-gray-400" />
                                <span>Linked to {{ $totalApps }} {{ Str::plural('application', $totalApps) }}</span>
                            </span>
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-gray-400 transition-transform {{ $expandedDocId === $doc->id ? 'rotate-180' : '' }}" />
                        </button>

                        @if($expandedDocId === $doc->id)
                            <div class="mt-2 p-2 rounded-lg border border-gray-200 bg-white space-y-1.5 text-xs max-h-40 overflow-y-auto">
                                @forelse($doc->applications as $linkedApp)
                                    @php
                                        $sEnum = $linkedApp->status instanceof \App\Enums\ApplicationStatus ? $linkedApp->status : \App\Enums\ApplicationStatus::tryFrom($linkedApp->status);
                                    @endphp
                                    <div class="flex items-center justify-between p-1.5 rounded hover:bg-gray-50">
                                        <a href="{{ route('applications.show', $linkedApp->id) }}" wire:navigate class="font-medium text-gray-800 hover:text-indigo-600 truncate max-w-[170px]">
                                            {{ $linkedApp->company->name ?? 'Company' }} — {{ $linkedApp->job_title }}
                                        </a>
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 shrink-0">
                                            {{ $sEnum ? $sEnum->label() : ucfirst($linkedApp->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-gray-400 italic p-1">No applications currently linked.</p>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    {{-- Actions Bar --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                        <a
                            href="{{ route('documents.download', $doc->id) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            Download
                        </a>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                wire:click="openEdit('{{ $doc->id }}')"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                                title="Rename Label"
                            >
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                wire:click="confirmDelete('{{ $doc->id }}')"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                title="Delete Document"
                            >
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Upload Slide-over --}}
    <x-slide-over
        id="upload-document-slide-over"
        title="Upload Document"
        wire:model="showUploadSlideOver"
    >
        <form wire:submit="saveUpload" class="space-y-5">
            {{-- Document Label --}}
            <div>
                <label for="doc-label" class="block text-sm font-medium text-gray-700 mb-1">Document Label *</label>
                <input
                    type="text"
                    id="doc-label"
                    wire:model="label"
                    placeholder="e.g., Resume v3 — Backend Focus"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    required
                >
                @error('label') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Document Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all {{ $docType === 'resume' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 font-semibold' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        <input type="radio" wire:model="docType" value="resume" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm">Resume</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all {{ $docType === 'cover_letter' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 font-semibold' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        <input type="radio" wire:model="docType" value="cover_letter" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm">Cover Letter</span>
                    </label>
                </div>
                @error('docType') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- File Upload Drag & Drop Zone --}}
            <div>
                <label for="doc-file-input" class="block text-sm font-medium text-gray-700 mb-1">File * (.PDF, .DOC, .DOCX - Max 5MB)</label>
                
                <div
                    x-data="{ isDragging: false }"
                    x-on:dragover.prevent="isDragging = true"
                    x-on:dragleave.prevent="isDragging = false"
                    x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    :class="isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400 bg-gray-50'"
                    class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer flex flex-col items-center justify-center"
                    @click="$refs.fileInput.click()"
                >
                    <input
                        type="file"
                        id="doc-file-input"
                        x-ref="fileInput"
                        wire:model="file"
                        accept=".pdf,.doc,.docx"
                        class="hidden"
                    >

                    <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 shadow-2xs flex items-center justify-center text-gray-500 mb-2">
                        <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-indigo-600" />
                    </div>

                    <p class="text-xs font-semibold text-gray-700">
                        Click to upload <span class="font-normal text-gray-500">or drag and drop</span>
                    </p>
                    <p class="text-[11px] text-gray-400 mt-1">PDF, DOC, DOCX up to 5MB</p>

                    {{-- Loading Indicator during file upload --}}
                    <div wire:loading wire:target="file" class="mt-2 text-xs text-indigo-600 font-semibold flex items-center gap-1.5">
                        <svg class="animate-spin w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Uploading file...
                    </div>

                    {{-- Selected file preview --}}
                    @if ($file)
                        <div class="mt-3 p-2 rounded-lg bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-900 flex items-center gap-2">
                            <x-heroicon-o-paper-clip class="w-4 h-4 text-indigo-600 shrink-0" />
                            <span class="truncate max-w-[200px]">{{ $file->getClientOriginalName() }}</span>
                            <span class="text-indigo-600 font-normal">({{ round($file->getSize() / 1024, 0) }} KB)</span>
                        </div>
                    @endif
                </div>
                @error('file') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Action buttons --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="$wire.set('showUploadSlideOver', false)"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all flex items-center gap-2"
                >
                    Save & Upload
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- Edit Label Slide-over --}}
    <x-slide-over
        id="edit-document-slide-over"
        title="Rename Document Label"
        wire:model="showEditModal"
    >
        <form wire:submit="saveEdit" class="space-y-5">
            <div>
                <label for="edit-doc-label" class="block text-sm font-medium text-gray-700 mb-1">Document Label *</label>
                <input
                    type="text"
                    id="edit-doc-label"
                    wire:model="editLabel"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    required
                >
                @error('editLabel') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="$wire.set('showEditModal', false)"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all"
                >
                    Update Label
                </button>
            </div>
        </form>
    </x-slide-over>

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal
        wire:model="showDeleteModal"
        title="Delete Document"
        message="Are you sure you want to delete {{ $this->deletingDocument?->label }}? The file will be permanently removed. This document is currently linked to {{ $this->deletingDocument?->applications_count ?? 0 }} application(s)."
        confirmText="Delete Document"
        confirmAction="delete"
    />
</div>
