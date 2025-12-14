<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;
use App\Models\Orangtua;

class OrangtuaExcelExport
{
    public static function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================================================
        // 1. JUDUL HEADER
        // ================================================
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'DATA ORANG TUA SANTRI ASRAMA MI QUR\'AN AL-FALAH');

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
            'B2' => 'NAMA AYAH',
            'C2' => 'PENDIDIKAN AYAH',
            'D2' => 'PEKERJAAN AYAH',
            'E2' => 'NAMA IBU',
            'F2' => 'PENDIDIKAN IBU',
            'G2' => 'PEKERJAAN IBU',
            'H2' => 'ALAMAT',
            'I2' => 'NAMA WALI',
            'J2' => 'PEKERJAAN WALI',
            'K2' => 'ALAMAT WALI',
            'L2' => 'NO HP',
            'M2' => 'ALAMAT EMAIL',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style header
        $sheet->getStyle('A2:M2')->applyFromArray([
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
        $data = Orangtua::all();
        $row = 3;
        $no = 1;

        foreach ($data as $s) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $s->nama_ayah);
            $sheet->setCellValue("C{$row}", $s->pendidikan_ayah);
            $sheet->setCellValue("D{$row}", $s->pekerjaan_ayah);
            $sheet->setCellValue("E{$row}", $s->nama_ibu);
            $sheet->setCellValue("F{$row}", $s->pendidikan_ibu);
            $sheet->setCellValue("G{$row}", $s->pekerjaan_ibu);
            $sheet->setCellValue("H{$row}", $s->alamat);
            $sheet->setCellValue("I{$row}", $s->nama_wali);
            $sheet->setCellValue("J{$row}", $s->pekerjaan_wali);
            $sheet->setCellValue("K{$row}", $s->alamat_wali);
            $sheet->setCellValue("L{$row}", $s->no_hp);
            $sheet->setCellValue("M{$row}", optional($s->user)->email);

            // Style baris data
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
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
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(26);
        $sheet->getColumnDimension('E')->setWidth(28);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(26);
        $sheet->getColumnDimension('H')->setWidth(28);
        $sheet->getColumnDimension('I')->setWidth(26);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(18);

        // ================================================
        // 5. SIMPAN FILE
        // ================================================
        $fileName = 'data_orangtua_siswa_' . date('Ymd') . '.xlsx';
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
