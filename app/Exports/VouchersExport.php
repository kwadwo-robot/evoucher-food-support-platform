<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VouchersExport implements FromCollection, WithHeadings, WithStyles
{
    private $vouchers;

    public function __construct($vouchers)
    {
        $this->vouchers = $vouchers;
    }

    public function collection()
    {
        return $this->vouchers->map(function ($voucher) {
            return [
                'Code' => $voucher->code,
                'Recipient' => $voucher->recipient ? $voucher->recipient->name : 'N/A',
                'Email' => $voucher->recipient ? $voucher->recipient->email : 'N/A',
                'Amount' => '£' . number_format($voucher->amount, 2),
                'Status' => ucfirst(str_replace('_', ' ', $voucher->status)),
                'Created' => $voucher->created_at->format('d M Y'),
                'Expires' => $voucher->expires_at ? $voucher->expires_at->format('d M Y') : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Voucher Code',
            'Recipient Name',
            'Recipient Email',
            'Amount',
            'Status',
            'Issued Date',
            'Expiry Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
            ],
        ];
    }
}
