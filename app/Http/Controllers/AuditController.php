<?php

namespace App\Http\Controllers;

use App\Audit;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auditLogs = Audit::with('user')->get();
        return view('audit.index')->with('auditLogs', $auditLogs);
    }
}
