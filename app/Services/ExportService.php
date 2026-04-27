<?php

namespace App\Services;

use App\Models\Trajet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportService
{
    /**
     * Generate an Excel file for "Frais de déplacement" based on a standardized template.
     */
    public function generateExpenseReportExport()
    {
        // Chemin vers votre modèle standardisé
        $templatePath = storage_path('app/templates/CPTA_FRAIS_DEPL_2025_PERSONNEL.xltx');

        // Load the template
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Fetch data from the database
        $trajets = Trajet::orderBy('date', 'asc')->get();

        // Injection data into the template
        $startRow = 10;
        foreach ($trajets as $index => $trajet) {
            $currentRow = $startRow + $index;

            $sheet->setCellValue("A{$currentRow}", $trajet->date);
            $sheet->setCellValue("B{$currentRow}", $trajet->depart);
            $sheet->setCellValue("C{$currentRow}", $trajet->arrivee);
            $sheet->setCellValue("D{$currentRow}", $trajet->distance);
        }

        return $spreadsheet;
    }
}
