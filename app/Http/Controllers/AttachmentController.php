<?php

namespace App\Http\Controllers;

use App\Models\AnswerAttachment;
use App\Models\QuestionAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function question(Request $request, QuestionAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment->question);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    public function answer(Request $request, AnswerAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment->answer->question);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }
}
