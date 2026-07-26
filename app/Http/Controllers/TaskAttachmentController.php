<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskAttachmentRequest;
use App\Http\Requests\UpdateTaskAttachmentRequest;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(StoreTaskAttachmentRequest $request, Task $task): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('task-attachments', 'public');

        $task->attachments()->create([
            'uploaded_by' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => intdiv($file->getSize(), 1024),
            'uploaded_at' => now(),
            'is_verified' => false,
            'meta' => [
                'mime_type' => $file->getClientMimeType(),
                'checksum' => hash_file('sha256', $file->getRealPath()),
            ],
        ]);

        return back()->with('success', 'File berhasil diupload.');
    }

    public function update(UpdateTaskAttachmentRequest $request, TaskAttachment $attachment): RedirectResponse
    {
        $attachment->update($request->validated());

        return back()->with('success', 'Status verifikasi diupdate.');
    }

    public function destroy(TaskAttachment $attachment): RedirectResponse
    {
        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return back()->with('success', 'Attachment berhasil dihapus.');
    }
}
