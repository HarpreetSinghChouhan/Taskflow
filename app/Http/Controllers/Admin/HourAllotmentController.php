<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskHourAllotment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HourAllotmentController extends Controller
{
    /**
     * Pending requests and recent decisions.
     */
    public function index(): View
    {
        $pending = TaskHourAllotment::where('status', 'pending')
            ->with(['task.project', 'task.employee', 'requester'])
            ->oldest('id')
            ->get();

        $history = TaskHourAllotment::where('status', '!=', 'pending')
            ->with(['task.project', 'requester', 'decider'])
            ->latest('decided_at')
            ->limit(20)
            ->get();

        return view('admin.hours.index', compact('pending', 'history'));
    }

    /**
     * Approve (max 40 hours) or reject a request.
     */
    public function decide(Request $request, TaskHourAllotment $allotment): RedirectResponse
    {
        if ($allotment->status !== 'pending') {
            return back()->with('error', 'This request is already decided.');
        }

        $data = $request->validate([
            'decision'       => ['required', 'in:approve,reject'],
            'approved_hours' => ['required_if:decision,approve', 'nullable', 'numeric', 'min:0.25', 'max:40'],
            'admin_note'     => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
        ], [
            'approved_hours.max'     => 'You can add at most 40 hours (one week) at a time.',
            'admin_note.required_if' => 'Please write a reason for rejecting.',
        ]);

        $approve = $data['decision'] === 'approve';

        $allotment->update([
            'status'         => $approve ? 'approved' : 'rejected',
            'approved_hours' => $approve ? $data['approved_hours'] : null,
            'admin_note'     => $data['admin_note'] ?? null,
            'decided_by'     => Auth::id(),
            'decided_at'     => now(),
        ]);

        return back()->with('success', $approve
            ? "{$data['approved_hours']} hours approved."
            : 'Request rejected.');
    }
}