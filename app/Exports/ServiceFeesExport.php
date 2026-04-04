<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceFeesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->shop ? $transaction->shop->name : 'N/A',
                '£' . number_format($transaction->total_amount, 2),
                $transaction->service_fee_percentage . '%',
                '£' . number_format($transaction->service_fee_amount, 2),
                '£' . number_format($transaction->amount_after_fee, 2),
                ucfirst($transaction->status),
                $transaction->created_at->format('M d, Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Shop Name',
            'Total Amount',
            'Fee Percentage',
            'Fee Amount',
            'Amount After Fee',
            'Status',
            'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
