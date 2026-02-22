<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class FinancialExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Order::with('customer');

        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order ID',
            'Customer',
            'Status',
            'Payment Method',
            'Revenue',
            'Real Cost (COGS)',
            'Product Profit',
            'Delivery Cost',
            'Net Profit'
        ];
    }

    public function map($order): array
    {
        return [
            $order->created_at->format('Y-m-d H:i'),
            $order->order_number,
            $order->customer->name ?? 'Guest',
            strtoupper($order->status),
            strtoupper($order->payment_method),
            $order->total,
            $order->total_cost,
            $order->total_profit,
            $order->delivery_price,
            ($order->total - $order->total_cost - $order->delivery_price)
        ];
    }
}
