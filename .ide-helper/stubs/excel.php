<?php

/**
 * IDE Helper Stubs for Maatwebsite/Excel and PhpSpreadsheet
 * These stubs help IDEs understand the class structure.
 * They have NO runtime effect.
 */

namespace Maatwebsite\Excel\Concerns;

interface FromCollection
{
    /** @return \Illuminate\Support\Collection */
    public function collection();
}

interface WithHeadings
{
    public function headings(): array;
}

interface WithMapping
{
    public function map($row): array;
}

interface ShouldAutoSize {}

interface WithStyles
{
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet);
}

interface FromArray
{
    public function array(): array;
}

interface WithTitle
{
    public function title(): string;
}

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Worksheet
{
    public function getStyle($cells) {}
    public function getColumnDimension(string $column) {}
    public function getRowDimension(int $row) {}
    public function setCellValue($coordinate, $value) {}
    public function freezePane(string $cell) {}
}

namespace Maatwebsite\Excel\Facades;

class Excel
{
    public static function download($export, string $fileName, string $writerType = 'Xlsx') {}
    public static function store($export, string $filePath, string $disk = 'local', string $writerType = 'Xlsx') {}
    public static function import($import, $filePath, string $disk = 'local', string $readerType = null) {}
}
