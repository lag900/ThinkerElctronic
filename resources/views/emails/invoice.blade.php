<!DOCTYPE html>
<html dir="{{ $order->customer->language === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>Protocol Invoice - {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        @page { margin: 0px; }
        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 60px;
            background: #fff;
            color: #222;
        }
        .header {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 40px;
            margin-bottom: 50px;
        }
        .logo { height: 65px; }
        .invoice-title {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            text-transform: uppercase;
            color: #222;
        }
        .grid { width: 100%; margin-bottom: 50px; }
        .col { vertical-align: top; width: 50%; }
        .label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #aaa;
            margin-bottom: 8px;
        }
        .value { font-size: 14px; font-weight: 700; color: #222; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
        }
        .table th {
            text-align: left;
            padding: 15px 0;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #aaa;
            border-bottom: 1px solid #f0f0f0;
        }
        .table td {
            padding: 25px 0;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13px;
            font-weight: 700;
        }
        .price-col { text-align: right; }
        .total-area {
            text-align: right;
            border-top: 1px solid #222;
            padding-top: 30px;
        }
        .grand-total-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .grand-total-value {
            font-size: 38px;
            font-weight: 900;
            color: #ff2d55;
            margin-top: 5px;
        }
        .footer {
            margin-top: 100px;
            padding-top: 30px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
            font-size: 10px;
            font-weight: 900;
            color: #ccc;
            letter-spacing: 5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><img src="{{ public_path('images/logo.png') }}" class="logo"></td>
                <td style="text-align: right;"><div class="invoice-title">INVOICE</div></td>
            </tr>
        </table>
    </div>

    <table class="grid">
        <tr>
            <td class="col">
                <div class="label">Billed To</div>
                <div class="value">{{ $order->customer->name }}</div>
                <div class="value" style="color: #666; font-weight: 400;">{{ $order->customer->email }}</div>
                <div class="value" style="color: #666; font-weight: 400;">{{ $order->customer->phone }}</div>
            </td>
            <td class="col" style="text-align: right;">
                <div class="label">Protocol ID</div>
                <div class="value">#{{ $order->order_number }}</div>
                <div style="margin-top: 20px;"></div>
                <div class="label">Date Executed</div>
                <div class="value">{{ $order->created_at->format('M d, Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 60%;">Product Description</th>
                <th style="text-align: center;">Quantity</th>
                <th class="price-col">Unit Price</th>
                <th class="price-col">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td class="price-col">{{ number_format($item->price, 2) }}</td>
                <td class="price-col">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-area">
        <div class="grand-total-label">Subtotal: {{ number_format($order->subtotal, 2) }}</div>
        <div class="grand-total-label">Shipping: FREE</div>
        <div class="grand-total-value">{{ number_format($order->total, 2) }} <span style="font-size: 14px;">EGP</span></div>
    </div>

    <div class="footer">
        THINKER ELECTRONIC SYSTEMS / ARCHITECTURAL NODE
    </div>
</body>
</html>
