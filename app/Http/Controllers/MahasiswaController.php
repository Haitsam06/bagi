<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::all();
        return response()->json($mahasiswa);
        // return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::create($request->all());
        return response()->json($mahasiswa, 201);
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::find($id);
        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa not found'], 404);
        }
        return response()->json($mahasiswa);
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::find($id);
        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa not found'], 404);
        }
        $mahasiswa->update($request->all());
        return response()->json($mahasiswa);
    }

    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::find($id);
        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa not found'], 404);
        }
        $mahasiswa->delete();
        return response()->json(['message' => 'Mahasiswa deleted']);
    }

    public function export_excel()
    {
        // Ambil semua data mahasiswa
        $mahasiswa = Mahasiswa::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header sesuai field di model
        $headers = ['ID', 'Nama', 'NIM', 'Jenis Kelamin', 'Usia', 'Program Studi'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Isi data
        $row = 2;
        foreach ($mahasiswa as $mhs) {
            $sheet->setCellValue('A' . $row, (string) $mhs->_id);
            $sheet->setCellValue('B' . $row, $mhs->nama);
            $sheet->setCellValue('C' . $row, $mhs->nim);
            $sheet->setCellValue('D' . $row, $mhs->jenis_kelamin);
            $sheet->setCellValue('E' . $row, $mhs->usia);
            $sheet->setCellValue('F' . $row, $mhs->prodi['nama'] ?? $mhs->prodi); // jika prodi berupa array/objek
            $row++;
        }

        // Auto-size kolom
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_mahasiswa_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function export_pdf()
    {
        // Ambil semua data mahasiswa
        $mahasiswa = Mahasiswa::all();

        // Generate PDF menggunakan dompdf
        $pdf = Pdf::loadView('mahasiswa_pdf', compact('mahasiswa'));

        // Download PDF
        return $pdf->download('data_mahasiswa_' . date('Ymd_His') . '.pdf');
    }
}
