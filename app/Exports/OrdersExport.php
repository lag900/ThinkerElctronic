<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class OrdersExport
 * @package App\Exports
 */
class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $search = $this->request->input('search');
        $employeeId = $this->request->input('employee_id');
        $status = $this->request->input('status');
        $startDate = $this->request->input('start_date');
        $endDate = $this->request->input('end_date');

        return Order::with(['customer', 'creator'])
            ->when($search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
            })
            ->when($employeeId, function ($query, $employeeId) {
                $query->where('created_by', $employeeId);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Customer',
            'Phone',
            'Status',
            'Payment Method',
            'Subtotal',
            'Delivery',
            'Final Total',
            'Profit',
            'Date'
        ];
    }

    /**
     * @param mixed $order
     * @return array
     */
    public function map($order): array
    {
        return [
            (string)$order->order_number,
            (string)($order->customer->name ?? 'Guest'),
            (string)($order->customer->phone ?? 'N/A'),
            strtoupper((string)$order->status),
            strtoupper((string)$order->payment_method),
            number_format((float)($order->subtotal ?? 0), 2) . ' EGP',
            number_format((float)($order->delivery_price ?? 0), 2) . ' EGP',
            number_format((float)($order->final_total ?? 0), 2) . ' EGP',
            number_format((float)($order->total_profit ?? 0), 2) . ' EGP',
            $order->created_at ? $order->created_at->format('Y-m-d H:i') : ''
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
