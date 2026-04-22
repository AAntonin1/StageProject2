<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ExpenseReportController extends Controller
{
    public function index()
    {

        return Inertia::render('ExpenseReports/Main', [
        ]);
    }

    public function store(){

    }
}
