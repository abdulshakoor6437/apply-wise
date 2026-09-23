<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    /**
     * Handle the incoming request to download a private document.
     */
    public function __invoke(Request $request, Document $document): StreamedResponse
    {
        abort_if($document->user_id !== Auth::id(), 403, 'Unauthorized access to this document.');

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found in storage.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }
}
