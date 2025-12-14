<?php

namespace App\Exports;

use App\Models\Kamar;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;

class KamarExcelExport
{
    public static function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================================================
        // 1. JUDUL HEADER
        // ================================================
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'DATA KAMAR ASRAMA MI QUR\'AN AL-FALAH');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // ================================================
        // 2. HEADER TABEL
        // ================================================
        $headers = [
            'A2' => 'NO',
            'B2' => 'NAMA KAMAR',
            'C2' => 'KAPASITAS',
            'D2' => 'JENIS KELAMIN',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style header
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Times New Roman',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['rgb' => '009933'], // hijau
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);

        // ================================================
        // 3. ISI DATA
        // ================================================
        $data = Kamar::all();
        $row = 3;
        $no = 1;

        foreach ($data as $s) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $s->nama_kamar);
            $sheet->setCellValue("C{$row}", $s->kapasitas);
            $sheet->setCellValue("D{$row}", $s->jenis_kelamin);

            // Style baris data
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 12],
                'alignment' => [
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ]
                ]
            ]);

            // Kolom nomor dibuat center
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // ================================================
        // 4. LEBAR KOLOM
        // ================================================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(24);

        // ================================================
        // 5. SIMPAN FILE
        // ================================================
        $fileName = 'data_kamar_asrama_' . date('Ymd') . '.xlsx';
        $path = storage_path("app/exports/{$fileName}");

        // Pastikan folder ada
        if (!is_dir(storage_path("app/exports"))) {
            mkdir(storage_path("app/exports"), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }
}
