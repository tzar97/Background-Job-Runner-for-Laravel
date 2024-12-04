<?php

namespace App\Http\Controllers;

use App\Models\BackgroundJob;
use Illuminate\Http\Request;

class BackgroundJobController extends Controller
{
    public function index()
    {
        $jobs = BackgroundJob::orderBy('created_at', 'desc')->paginate(10);
        return view('background_jobs.index', compact('jobs'));
    }

    public function show(BackgroundJob $job)
    {
        return view('background_jobs.show', compact('job'));
    }

    public function cancel(BackgroundJob $job)
    {
        if (in_array($job->status, ['pending', 'running'])) {
            $job->status = 'cancelled';
            $job->save();
            return redirect()->back()->with('success', 'cancelled successfully.');
        }

        return redirect()->back()->with('error', 'cant cancelled.');
    }
}
