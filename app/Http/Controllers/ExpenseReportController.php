<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseReportController extends Controller
{
    public function index()
    {

        return Inertia::render('ExpenseReports/Main', [
        ]);
    }

    public function store(Request $request)
    {

        $segments = $request->input('segments');
        foreach ($segments as $segment) {
            Segment::create([
                'date' => $request->input('date'), // TODO: Add date for ech segment if needed
                'from_address' => $segment['from_address']['label'],
                'to_address' => $segment['to_address']['label'],
                'departure_time' => $segment['departure_time'],
                'arrival_time' => $segment['arrival_time'],
                'reason' => $segment['reason'],
                'distance_km' => $segment['distance'],
                'time_btw' => $segment['timeBtw'],
                'type_doc' => $segment['typeDoc'],
                'expense_report_id' => 1, //TODO: replace with actual expense report ID after creating the expense report
            ]);
        }

        return redirect()->route('home')->with('success', 'Expense report created successfully!');

    }
}
