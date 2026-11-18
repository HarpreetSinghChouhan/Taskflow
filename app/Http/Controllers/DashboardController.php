<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin Master Dashboard (Command Center)
     */
    public function admin(): View
    {
        return view('admin.dashboard', [
            'user'          => Auth::user(),
            'totalUsers'    => User::count(),
            'pmCount'       => User::where('role', 'project_manager')->count(),
            'tlCount'       => User::where('role', 'team_leader')->count(),
            'employeeCount' => User::where('role', 'employee')->count(),
            'recentUsers'   => User::latest('id')->take(6)->get(),
        ]);
    }

    /**
     * Project Manager Dashboard (Sprint & Delivery Oversight)
     */
    public function pm(): View
    {
        return view('pm.dashboard', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Team Leader Dashboard (Sub-Team & Code Reviews)
     */
    public function tl(): View
    {
        return view('tl.dashboard', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Employee / Developer Workspace (Personal Sprints & Tasks)
     */
    public function employee(): View
    {
        return view('employee.dashboard', [
            'user' => Auth::user(),
        ]);
    }
}
