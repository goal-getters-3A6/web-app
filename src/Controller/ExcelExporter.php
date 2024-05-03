<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExporter
{
    public function exportPlatsToExcel(array $plats): Response
    {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $sheet->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prix')
            ->setCellValue('C1', 'Description')
            ->setCellValue('D1', 'Allergies')
            ->setCellValue('E1', 'en stock?');
           

      
        $row = 2;
        foreach ($plats as $plat) {
            $sheet->setCellValue('A' . $row, $plat->getNomp())
                ->setCellValue('B' . $row, $plat->getPrixp())
                ->setCellValue('C' . $row, $plat->getDescp())
                ->setCellValue('D' . $row, $plat->getAlergiep())
                ->setCellValue('E' . $row, $plat->isEtatp() ? 'oui' : 'non');
            
            $row++;
        }

    
        $filePath = 'C:\plats.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

       
        $response = new Response();

       
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'plats.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        $response->setContent(file_get_contents($filePath));

       
        unlink($filePath);

        return $response;
    }
}
