<?php

namespace App\Services;

use App\Models\Expense_report;
use App\Models\Segment;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportService
{
    /**
     * Generate an Excel file for "Frais de déplacement" based on a standardized template.
     */
    public function generateExpenseReportExport()
    {
        // Path to the standardized template
        $templatePath = storage_path('app/Templates/CPTA_FRAIS_DEPL_2025_PERSONNEL.xltx');

        // Load the template
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Get User info
        $user = User::where('id', 4)->first();

        // Fetch data from the database
        $segments = Segment::orderBy('date', 'asc')
            ->where('user_id', $user->id)
            ->get();

        $expense_report = Expense_report::where('user_id', $user->id)
            ->where('month_year', date('m/Y'))
            ->first();

        $sheet->setCellValue('C3', $user->first_name.' '.$user->last_name);
        $sheet->setCellValue('B5', $user->address_home);
        $sheet->setCellValue('B5', $user->address_home);
        $sheet->setCellValue('E4', $expense_report->address_work);
        $sheet->setCellValue('E5', $expense_report->job);
        $sheet->setCellValue('G3', $expense_report->vehicle);
        $sheet->setCellValue('G4', $expense_report->number_plate);

        $currentRow = 9;

        foreach ($segments as $segment) {
            $sheet->setCellValue('A'.$currentRow, 'motif du déplacement: '.$segment->reason);

            // Fill in the segment details
            $currentRow++;
            $sheet->setCellValue('B'.$currentRow, Carbon::parse($segment->date)->format('d/m/Y'));
            $sheet->setCellValue('C'.$currentRow, $segment->from_address);
            $sheet->setCellValue('D'.$currentRow, $segment->departure_time);
            $sheet->setCellValue('E'.$currentRow, $segment->to_address);
            $sheet->setCellValue('F'.$currentRow, $segment->arrival_time);
            $sheet->setCellValue('G'.$currentRow, $segment->distance_km);

            // Jump to next line for return trip
            $currentRow += 2;

            if ($currentRow > 40) {
                break;
            }
        }

        return $spreadsheet;
    }
}
