<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;

class SiswaExcelExport
{
    public static function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================================================
        // 1. JUDUL HEADER
        // ================================================
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'DATA SANTRIWAN/SANTRIWATI ASRAMA MI QUR\'AN AL-FALAH');

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
            'B2' => 'NAMA LENGKAP',
            'C2' => 'NAMA PANGGILAN',
            'D2' => 'ORANG TUA',
            'E2' => 'NIS',
            'F2' => 'TTL',
            'G2' => 'JENIS KELAMIN',
            'H2' => 'KEWARGANEGARAAN',
            'I2' => 'STATUS KELUARGA',
            'J2' => 'STATUS ORANGTUA',
            'K2' => 'ANAK KE',
            'L2' => 'TANGGAL MASUK',
            'M2' => 'KELAS',
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
        $data = Siswa::all();
        $row = 3;
        $no = 1;

        foreach ($data as $s) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $s->nama_lengkap);
            $sheet->setCellValue("C{$row}", $s->nama_panggilan);
            $sheet->setCellValue("D{$row}", optional($s->user)->name);
            $sheet->setCellValue("E{$row}", $s->nis);
            $sheet->setCellValue("F{$row}", $s->tempat_lahir . "\n" . $s->tanggal_lahir->format('d M Y'));
            $sheet->setCellValue("G{$row}", $s->jenis_kelamin);
            $sheet->setCellValue("H{$row}", $s->kewarganegaraan);
            $sheet->setCellValue("I{$row}", $s->status_keluarga);
            $sheet->setCellValue("J{$row}", $s->status_orangtua);
            $sheet->setCellValue("K{$row}", $s->anak_ke);
            $sheet->setCellValue("L{$row}", $s->tgl_masuk ? \Carbon\Carbon::parse($s->tgl_masuk)->format('d M Y') : '-');
            $sheet->setCellValue("M{$row}", $s->kelas);

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
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(26);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(12);

        // ================================================
        // 5. SIMPAN FILE
        // ================================================
        $fileName = 'data_siswa_asrama_' . date('Ymd') . '.xlsx';
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
