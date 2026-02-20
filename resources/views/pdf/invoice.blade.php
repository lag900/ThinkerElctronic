<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --red: #e10600;
            --black: #111111;
            --gray: #f5f5f5;
            --text-dark: #000000;
            --text-muted: #777777;
            --white: #ffffff;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            color: var(--black);
            margin: 0;
            padding: 50px;
            background-color: var(--white);
            line-height: 1.2;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .invoice-label {
            font-size: 32px;
            font-weight: 900;
            color: var(--red);
            text-transform: uppercase;
            margin: 0;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .top-divider {
            width: 100%;
            height: 1px;
            background-color: #eeeeee;
            margin: 20px 0 40px 0;
        }

        .main-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 50px;
        }

        .meta-section {
            width: 50%;
        }

        .meta-item {
            margin-bottom: 15px;
        }

        .meta-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .billing-section {
            width: 40%;
            text-align: right;
        }

        [dir="rtl"] .billing-section {
            text-align: left;
        }

        .billing-label {
            font-size: 10px;
            font-weight: 800;
            color: var(--text-dark);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .customer-name {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .customer-details {
            font-size: 12px;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .products-table th {
            background-color: var(--red);
            color: var(--white);
            text-align: left;
            padding: 12px 15px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
        }

        [dir="rtl"] .products-table th {
            text-align: right;
        }

        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #eeeeee;
            font-size: 12px;
            color: var(--text-dark);
            font-weight: 800;
        }

        .summary-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-bottom: 50px;
        }

        [dir="rtl"] .summary-section {
            align-items: flex-start;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            width: 250px;
            margin-bottom: 8px;
        }

        .summary-label {
            font-size: 10px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .total-container {
            margin-top: 10px;
            text-align: right;
            width: 250px;
            border-top: 3px solid var(--red);
            padding-top: 15px;
        }

        [dir="rtl"] .total-container {
            text-align: left;
        }

        .total-label {
            font-size: 10px;
            font-weight: 900;
            color: var(--red);
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 900;
            color: var(--red);
            border-bottom: 2px solid var(--black);
            display: inline-block;
            padding-bottom: 5px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }

        .footer-quote {
            font-size: 14px;
            font-weight: 700;
            color: #999999;
            margin-bottom: 5px;
            font-family: 'Cairo', sans-serif;
        }

        .footer-sub {
            font-size: 10px;
            font-weight: 600;
            color: #bbbbbb;
            margin-bottom: 2px;
        }

        .footer-thanks {
            font-size: 9px;
            color: #cccccc;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="invoice-label">{{ __('INVOICE') }}</h1>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="logo">
    </div>

    <div class="top-divider"></div>

    <div class="main-info">
        <div class="meta-section">
            <div class="meta-item">
                <div class="meta-label">{{ __('INVOICE NUMBER') }}</div>
                <div class="meta-value">#{{ $invoice->invoice_number }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">{{ __('ORDER DATE') }}</div>
                <div class="meta-value">{{ $invoice->created_at->format('d M, Y') }}</div>
            </div>
            <div class="meta-item" style="margin-bottom: 0;">
                <div class="meta-label">{{ __('PAYMENT METHOD') }}</div>
                <div class="meta-value">{{ strtolower($order->payment_method ?? 'COD') }}</div>
            </div>
        </div>

        <div class="billing-section">
            <div class="billing-label">{{ __('BILLED TO') }}</div>
            <div class="customer-name">{{ $order->customer->name ?? 'Guest User' }}</div>
            <div class="customer-details">{{ $order->customer->phone ?? '' }}</div>
            <div class="customer-details">{{ $order->customer->email ?? '' }}</div>
            <div class="customer-details">{{ $order->shipping_address ?? '' }}</div>
            <div class="customer-details">{{ $order->city ?? '' }}, {{ $order->province ?? '' }}</div>
        </div>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 55%;">{{ __('PRODUCT') }}</th>
                <th style="width: 10%; text-align: right;">{{ __('QTY') }}</th>
                <th style="width: 15%; text-align: right;">{{ __('PRICE') }}</th>
                <th style="width: 20%; text-align: right;">{{ __('TOTAL') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="product-name">{{ $item->product->name ?? 'Unknown Product' }}</td>
                <td class="qty" style="text-align: right;">{{ $item->quantity }}</td>
                <td class="amount" style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                <td class="amount" style="text-align: right;">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-label">{{ __('SUBTOTAL') }}</div>
            <div class="summary-value">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">{{ __('SHIPPING') }}</div>
            <div class="summary-value">{{ $invoice->shipping > 0 ? number_format($invoice->shipping, 2) . ' ' . $invoice->currency : __('FREE') }}</div>
        </div>
        
        <div class="total-container">
            <div class="total-label">{{ __('TOTAL AMOUNT') }}</div>
            <div class="total-amount">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-quote">تفكر ذكي.. أنظمة ذكية</div>
        <div class="footer-sub">Smart Thinking, Smart Systems.</div>
        <div class="footer-thanks">Thank you for choosing Thinker Electronics.</div>
    </div>
</body>
</html>
