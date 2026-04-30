<?php

namespace App\Http\Controllers;

use App\Models\Expense_report;
use App\Models\Segment;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Ajouté pour la sécurité des transactions
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $segments = Segment::where('user_id', $user->id)->get();
        $users = User::with('roles')->get();

        $expense_report = Expense_report::where('user_id', $user->id)
            ->where('month_year', date('m/Y'))
            ->first();

        return Inertia::render('ExpenseReports/Main', [
            'segments' => $segments,
            'user' => $user,
            'expense_report' => $expense_report,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'addressHome' => 'required',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'date' => 'required|date',
            'addressWork' => 'required',
            'job' => 'required|string',
            'vehicle' => 'required|string',
            'numberPlate' => 'required|string',
            'segments' => 'required|array',
            'segments.*.from_address.label' => 'required|string',
            'segments.*.to_address.label' => 'required|string',
            'segments.*.departure_time' => 'required',
            'segments.*.arrival_time' => 'required',
            'segments.*.reason' => 'required|string',
            'segments.*.distance' => 'required|numeric',
            'segments.*.timeBtw' => 'required|numeric',
            'segments.*.typeDoc' => 'required|string',
        ]);

        $userId = auth()->id();
        $dateInput = $request->input('date');
        $monthYear = date('m/Y', strtotime($dateInput));

        // Utilisation d'une transaction pour éviter les données partielles en cas de bug réseau
        DB::transaction(function () use ($request, $userId, $monthYear) {

            // Extraction propre des labels d'adresses (car ce sont des objets en JS)
            $addressWorkData = $request->input('addressWork');
            $workLabel = is_array($addressWorkData) ? ($addressWorkData['label'] ?? 'Non renseigné') : $addressWorkData;

            $expenseReport = Expense_report::firstOrCreate(
                [
                    'user_id' => $userId,
                    'month_year' => $monthYear,
                ],
                [
                    'date' => now(),
                    'status' => 'draft',
                    'address_work' => $workLabel,
                    'job' => $request->input('job'),
                    'vehicle' => $request->input('vehicle'),
                    'km_rate' => 0.4449, // Aligné avec votre JS
                    'total_km' => 0,
                    'total_amount' => 0,
                    'number_plate' => $request->input('numberPlate'),
                ]
            );

            $segments = $request->input('segments');
            foreach ($segments as $segment) {
                Segment::create([
                    'user_id' => $userId,
                    'date' => $request->input('date'),
                    'from_address' => $segment['from_address']['label'],
                    'to_address' => $segment['to_address']['label'],
                    'departure_time' => $segment['departure_time'],
                    'arrival_time' => $segment['arrival_time'],
                    'reason' => $segment['reason'],
                    'distance_km' => $segment['distance'],
                    'time_btw' => gmdate('H:i:s', (int) $segment['timeBtw']),
                    'type_doc' => $segment['typeDoc'],
                    'expense_report_id' => $expenseReport->id,
                ]);
            }

            // Mise à jour des infos utilisateur (on encode les adresses si ce sont des objets)
            User::where('id', $userId)->update([
                'first_name' => $request->input('firstName'),
                'last_name' => $request->input('lastName'),
                'address_home' => is_array($request->input('addressHome')) ? json_encode($request->input('addressHome')) : $request->input('addressHome'),
                'address_work' => is_array($request->input('addressWork')) ? json_encode($request->input('addressWork')) : $request->input('addressWork'),
            ]);

            // Recalcul des totaux
            $totalKm = Segment::where('expense_report_id', $expenseReport->id)->sum('distance_km');
            $expenseReport->update([
                'total_km' => $totalKm,
                'total_amount' => $totalKm * $expenseReport->km_rate,
            ]);
        });

        // Pour gérer à la fois Inertia (form.post) et Axios (sync offline)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Rapport enregistré avec succès !'], 201);
        }

        return redirect()->back()->with('success', 'Note de frais enregistrée, Mon Seigneur.');
    }

    public function export(ExportService $exportService): StreamedResponse
    {
        $spreadsheet = $exportService->generateExpenseReportExport();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Note_de_frais_2026.xlsx');
    }
}
