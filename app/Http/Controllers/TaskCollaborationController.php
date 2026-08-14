<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskChecklistItemRequest;
use App\Http\Requests\StoreTaskCommentRequest;
use App\Http\Requests\StoreTaskTimeLogRequest;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use App\Models\TaskComment;
use App\Models\TaskTimeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskCollaborationController extends Controller
{
    public function storeChecklist(StoreTaskChecklistItemRequest $request, Task $task): RedirectResponse
    {
        $task->checklistItems()->create([
            'description' => $request->validated('description'),
            'sort_order' => ((int) $task->checklistItems()->max('sort_order')) + 1,
        ]);

        return back()->with('status', 'Item checklist ditambahkan.');
    }

    public function toggleChecklist(Request $request, Task $task, TaskChecklistItem $checklistItem): RedirectResponse
    {
        $this->authorize('update', $task);
        abort_unless($checklistItem->task_id === $task->id, 404);
        $checklistItem->update(['is_finished' => ! $checklistItem->is_finished]);

        return back()->with('status', 'Status checklist diperbarui.');
    }

    public function destroyChecklist(Task $task, TaskChecklistItem $checklistItem): RedirectResponse
    {
        $this->authorize('update', $task);
        abort_unless($checklistItem->task_id === $task->id, 404);
        $checklistItem->delete();

        return back()->with('status', 'Item checklist dihapus.');
    }

    public function storeComment(StoreTaskCommentRequest $request, Task $task): RedirectResponse
    {
        $task->comments()->create(['user_id' => $request->user()->id, 'comment' => $request->validated('comment')]);

        return back()->with('status', 'Komentar ditambahkan.');
    }

    public function destroyComment(Request $request, Task $task, TaskComment $comment): RedirectResponse
    {
        abort_unless($comment->task_id === $task->id, 404);
        abort_unless($request->user()->can('update', $task) || $comment->user_id === $request->user()->id, 403);
        $comment->delete();

        return back()->with('status', 'Komentar dihapus.');
    }

    public function storeTimeLog(StoreTaskTimeLogRequest $request, Task $task): RedirectResponse
    {
        $task->timeLogs()->create(array_merge($request->validated(), ['user_id' => $request->user()->id]));

        return back()->with('status', 'Waktu kerja dicatat.');
    }

    public function stopTimeLog(Request $request, Task $task, TaskTimeLog $timeLog): RedirectResponse
    {
        abort_unless($timeLog->task_id === $task->id, 404);
        abort_unless($request->user()->can('update', $task) || $timeLog->user_id === $request->user()->id, 403);
        abort_if($timeLog->end_time, 422, 'Timer sudah dihentikan.');
        $timeLog->update(['end_time' => now()]);

        return back()->with('status', 'Timer dihentikan.');
    }

    public function destroyTimeLog(Request $request, Task $task, TaskTimeLog $timeLog): RedirectResponse
    {
        abort_unless($timeLog->task_id === $task->id, 404);
        abort_unless($request->user()->can('update', $task) || $timeLog->user_id === $request->user()->id, 403);
        $timeLog->delete();

        return back()->with('status', 'Catatan waktu dihapus.');
    }
}
