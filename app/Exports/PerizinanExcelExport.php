<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;
use App\Models\Perizinan;

class PerizinanExcelExport
{
    public static function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================================================
        // 1. JUDUL HEADER
        // ================================================
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'DATA PERIZINAN ASRAMA MI QUR\'AN AL-FALAH');

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
            'B2' => 'NAMA SISWA',
            'C2' => 'ALASAN',
            'D2' => 'TANGGAL KELUAR',
            'E2' => 'WAKTU KELUAR',
            'F2' => 'TANGGAL MASUK',
            'G2' => 'WAKTU MASUK',
            'H2' => 'STATUS',
            'I2' => 'ORANG TUA',
            'J2' => 'KETERANGAN',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style header
        $sheet->getStyle('A2:J2')->applyFromArray([
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
        $data = Perizinan::all();
        $row = 3;
        $no = 1;

        foreach ($data as $s) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $s->siswa->nama_lengkap);
            $sheet->setCellValue("C{$row}", $s->alasan);
            $sheet->setCellValue("D{$row}", $s->tanggal_keluar ? \Carbon\Carbon::parse($s->tanggal_keluar)->format('d M Y') : '-');
            $sheet->setCellValue("E{$row}", $s->waktu_keluar);
            $sheet->setCellValue("F{$row}", $s->tanggal_masuk ? \Carbon\Carbon::parse($s->tanggal_masuk)->format('d M Y') : '-');
            $sheet->setCellValue("G{$row}", $s->waktu_masuk);
            $sheet->setCellValue("H{$row}", $s->status);
            $sheet->setCellValue("I{$row}", $s->user->name);
            $sheet->setCellValue("J{$row}", $s->keterangan);

            // Style baris data
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
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
        $sheet->getColumnDimension('J')->setWidth(28);

        // ================================================
        // 5. SIMPAN FILE
        // ================================================
        $fileName = 'data_perizinan_asrama_' . date('Ymd') . '.xlsx';
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
