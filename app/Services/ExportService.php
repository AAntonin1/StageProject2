<?php

namespace App\Services;

use App\Models\Expense_report;
use App\Models\Segment;
use App\Models\User;
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
        $user = User::where('id', 1)->first();

        // Fetch data from the database
        $segments = Segment::orderBy('date', 'asc')
            ->where('user_id', $user->id)
            ->get();

        $expense_report = Expense_report::where('user_id', $user->id)
            ->where('month_year', date('m/Y'))
            ->first();

        // Injection data into the template
        //        $startRow = 10;
        //        foreach ($trajets as $index => $trajet) {
        //            $currentRow = $startRow + $index;
        //
        //            $sheet->setCellValue("A{$currentRow}", $trajet->date);
        //            $sheet->setCellValue("B{$currentRow}", $trajet->depart);
        //            $sheet->setCellValue("C{$currentRow}", $trajet->arrivee);
        //            $sheet->setCellValue("D{$currentRow}", $trajet->distance);
        //        }

        $sheet->setCellValue('C3', $user->first_name.' '.$user->last_name);
        $sheet->setCellValue('B5', $user->address_home);

        return $spreadsheet;
    }
}
