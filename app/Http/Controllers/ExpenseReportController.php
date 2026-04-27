<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ExportService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseReportController extends Controller
{
    public function index()
    {

        $user = auth()->user();
        $segments = Segment::where('user_id', $user->id)->get();

        return Inertia::render('ExpenseReports/Main', [
            'segments' => $segments,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'addressHome' => 'required',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'date' => 'required|date',
            'segments' => 'required|array',
            'segments.*.from_address.label' => 'required|string',
            'segments.*.to_address.label' => 'required|string',
            'segments.*.departure_time' => 'required|date_format:H:i',
            'segments.*.arrival_time' => 'required|date_format:H:i',
            'segments.*.reason' => 'required|string',
            'segments.*.distance' => 'required|numeric',
            'segments.*.timeBtw' => 'required|numeric',
            'segments.*.typeDoc' => 'required|string',
        ]);

        $segments = $request->input('segments');
        foreach ($segments as $segment) {
            Segment::create([
                'user_id' => auth()->id(),
                'date' => $request->input('date'), // TODO: Add date for ech segment if needed
                'from_address' => $segment['from_address']['label'],
                'to_address' => $segment['to_address']['label'],
                'departure_time' => $segment['departure_time'],
                'arrival_time' => $segment['arrival_time'],
                'reason' => $segment['reason'],
                'distance_km' => $segment['distance'],
                'time_btw' => $segment['timeBtw'],
                'type_doc' => $segment['typeDoc'],
                'expense_report_id' => 1, // TODO: replace with actual expense report ID after creating the expense report
            ]);
        }

        User::where('id', auth()->id())->update(['first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'address_home' => $request->input('addressHome.label'),
        ]);
        return redirect()->route('expenseReport.form')->with('success', 'Expense report created successfully!');

    }

    public function export(ExportService $exportService): StreamedResponse
    {
        $spreadsheet = $exportService->generateExpenseReportExport();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Note_de_frais_2025.xlsx');
    }
}
