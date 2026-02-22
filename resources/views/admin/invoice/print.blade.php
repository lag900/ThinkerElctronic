<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }} #{{ $order->invoice ? $order->invoice->invoice_number : $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
            color: #111;
            margin: 0;
            padding: 40px;
            font-size: 14px;
        }

        .receipt-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #f2f2f2;
            padding-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #111;
            margin-bottom: 5px;
        }

        .company-info {
            color: #666;
            font-size: 12px;
            line-height: 1.6;
        }

        .invoice-details {
            text-align: right;
        }
        
        [dir="rtl"] .invoice-details {
            text-align: left;
        }

        .invoice-title {
            font-size: 36px;
            font-weight: 800;
            color: #111;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-block h4 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #a3a3a3;
            margin-block-end: 10px;
            margin-top: 0;
        }
        
        .info-block p {
            margin: 0;
            font-weight: 600;
            line-height: 1.5;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #f9fafb;
            padding: 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            font-weight: 800;
            border-bottom: 2px solid #ececf2;
        }
        
        [dir="rtl"] th {
            text-align: right;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #ececf2;
            vertical-align: top;
        }

        .item-name {
            font-weight: 800;
            color: #111;
        }

        .num {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 13px;
        }

        .totals {
            width: 100%;
            max-width: 350px;
            margin-left: auto;
            border-top: 2px solid #111;
            padding-top: 20px;
        }
        
        [dir="rtl"] .totals {
            margin-left: 0;
            margin-right: auto;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-weight: 600;
            color: #333;
        }

        .totals-row.grand-total {
            font-size: 24px;
            font-weight: 800;
            color: #ff2b2b;
            padding-top: 20px;
            margin-top: 10px;
            border-top: 1px solid #ececf2;
        }

        .footer {
            text-align: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #ececf2;
            color: #a3a3a3;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .actions {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #111;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        
        .btn:hover {
            background: #ff2b2b;
        }

        .btn-outline {
            background: #fff;
            color: #111;
            border: 2px solid #ececf2;
            margin-left: 10px;
        }

        .btn-outline:hover {
            border-color: #111;
        }
        
        [dir="rtl"] .btn-outline {
            margin-left: 0;
            margin-right: 10px;
        }

        @media print {
            body { 
                background: white; 
                margin: 0; 
                padding: 0; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .receipt-wrapper { 
                border: none; 
                box-shadow: none; 
                margin: 0; 
                padding: 0; 
                max-width: 100%; 
            }
            .no-print { display: none !important; }
        }

        /* Dynamically injected direction classes */
        .text-dir {
            text-align: right;
        }
        [dir="rtl"] .text-dir {
            text-align: left;
        }
        
        .status-paid { color: #00b86b; }
        .status-unpaid { color: #ff2b2b; }
    </style>
</head>
<body onload="window.print()">

    <div class="actions no-print">
        <button onclick="window.print()" class="btn">🖨️ {{ __('Print Invoice') }}</button>
        <button onclick="window.print()" class="btn btn-outline" style="display:none;">⬇️ {{ __('Download PDF') }}</button>
        <!-- For downloading, the print dialog provides "Save as PDF" option directly -->
    </div>

    <div class="receipt-wrapper">
        <div class="header">
            <div>
                <div class="logo">{{ config('app.name', 'STORE NAME') }}</div>
                <div class="company-info">
                    {{ __('Official Retail Partner') }}<br>
                    support@{{ strtolower(str_replace(' ', '', config('app.name', 'store'))) }}.com<br>
                    {{ __('VAT No') }}: 123456789
                </div>
            </div>
            <div class="invoice-details">
                <div class="invoice-title">{{ __('INVOICE') }}</div>
                <div class="company-info">
                    <strong>#{{ $order->invoice ? $order->invoice->invoice_number : $order->order_number }}</strong><br>
                    {{ __('Date') }}: {{ $order->created_at->format('M d, Y') }}<br>
                    {{ __('Time') }}: {{ $order->created_at->format('H:i A') }}
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h4>{{ __('Billed To') }}</h4>
                <p>
                    {{ $order->customer ? $order->customer->name : __('Walk-in Customer') }}<br>
                    @if($order->customer && $order->customer->phone)
                        {{ $order->customer->phone }}<br>
                    @endif
                    @if($order->customer && $order->customer->email)
                        {{ $order->customer->email }}
                    @endif
                </p>
            </div>
            <div class="info-block">
                <h4>{{ __('Payment Details') }}</h4>
                <p>
                    {{ __('Method') }}: {{ strtoupper($order->payment_method ?? 'Cash') }}<br>
                    @php $statusClass = ($order->invoice && $order->invoice->status == 'paid') ? 'status-paid' : 'status-unpaid'; @endphp
                    {{ __('Status') }}: <span class="{{ $statusClass }}">{{ strtoupper($order->invoice ? $order->invoice->status : 'PAID') }}</span>
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="50%">{{ __('Item Description') }}</th>
                    <th width="15%" style="text-align: center;">{{ __('Qty') }}</th>
                    <th width="15%" class="text-dir">{{ __('Price') }}</th>
                    <th width="20%" class="text-dir">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product ? (app()->getLocale() === 'ar' && $item->product->name_ar ? $item->product->name_ar : $item->product->name) : 'Unknown Product' }}</div>
                    </td>
                    <td style="text-align: center;" class="num">{{ $item->quantity }}</td>
                    <td class="num text-dir">{{ number_format($item->price, 2) }}</td>
                    <td class="num text-dir"><strong>{{ number_format($item->price * $item->quantity, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>{{ __('Subtotal') }}</span>
                <span class="num">{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="totals-row" style="color: #ff2b2b;">
                <span>{{ __('Discount') }}</span>
                <span class="num">-{{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row grand-total">
                <span>{{ __('Final Total (EGP)') }}</span>
                <span class="num">{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            {{ __('Thank you for your business. For any inquiries, please contact our support team.') }}
        </div>
    </div>

</body>
</html>
