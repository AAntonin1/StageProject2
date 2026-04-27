<?php

namespace App\Http\Controllers;

use App\Models\Expense_report;
use App\Models\Segment;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseReportController extends Controller
{
    public function index()
    {

        $user = auth()->user();
        $segments = Segment::where('user_id', $user->id)->get();

        $expense_report = Expense_report::where('user_id', $user->id)
            ->where('month_year', date('m/Y'))
            ->first();

        return Inertia::render('ExpenseReports/Main', [
            'segments' => $segments,
            'user' => $user,
            'expense_report' => $expense_report,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'addressHome' => 'required',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'date' => 'required|date',
            'placeBusiness' => 'required|string',
            'job' => 'required|string',
            'vehicle' => 'required|string',
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

        $userId = auth()->id();
        $dateInput = $request->input('date'); // Format 'YYYY-MM-DD'
        $monthYear = date('m/Y', strtotime($dateInput));

        $expenseReport = Expense_report::firstOrCreate(
            [
                'user_id' => $userId,
                'month_year' => $monthYear,
            ],
            [
                'date' => now(),
                'status' => 'draft',
                'address_work' => $request->input('placeBusiness') ?? 'Non renseigné',
                'job' => $request->input('job') ?? 'Non renseigné',
                'vehicle' => $request->input('vehicle') ?? 'Non renseigné',
                'km_rate' => 0.42,
                'total_km' => 50, // TODO: replace with actual total km after creating segments
                'total_amount' => $request->input('total_km') * 0.42,
                'number_plate' => $request->input('number_plate') ?? 'Non renseigné',
            ]
        );

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
                'time_btw' => gmdate("H:i:s", (int)$segment['timeBtw']),
                'type_doc' => $segment['typeDoc'],
                'expense_report_id' => $expenseReport->id, // TODO: replace with actual expense report ID after creating the expense report
            ]);
        }

        User::where('id', auth()->id())->update(['first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'address_home' => $request->input('addressHome.label'),
        ]);

        $expenseReport->update([
            'total_km' => Segment::where('expense_report_id', $expenseReport->id)->sum('distance_km'),
            'total_amount' => Segment::where('expense_report_id', $expenseReport->id)->sum('distance_km') * $expenseReport->km_rate,
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
