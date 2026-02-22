<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', 'DejaVu Sans', sans-serif;
            color: #111111;
            margin: 0;
            padding: 0;
            font-size: 14px;
            background: #ffffff;
        }
        .container {
            width: 794px;
            margin: 0 auto;
            padding: 20px;
        }
        .text-red {
            color: #e11d48;
        }
        .bg-red {
            background-color: #e11d48;
            color: #ffffff;
        }
        .bold {
            font-weight: 700;
        }
        /* Top Header Grid */
        .header-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .header-section td {
            vertical-align: middle;
        }
        .title-td {
            width: 50%;
            text-align: left;
        }
        .logo-td {
            width: 50%;
            text-align: right;
        }
        .logo img {
            width: 160px;
            height: auto;
            object-fit: contain;
        }
        .header-title {
            font-size: 40px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
        }

        /* Info Section Grid */
        .info-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .info-section td {
            vertical-align: top;
        }
        .info-left {
            width: 50%;
            text-align: left;
        }
        .info-right {
            width: 50%;
            text-align: right;
        }
        span.meta-label {
            font-size: 12px;
            font-weight: 700;
            color: #777777;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        span.meta-value {
            font-size: 15px;
            font-weight: 700;
            color: #111111;
            display: block;
            margin-bottom: 20px;
        }
        .billed-to-name {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 10px;
            color: #111;
        }
        .billed-to-text {
            font-size: 14px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        /* Products Table */
        table.products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            direction: ltr; /* Force layout structure */
        }
        table.products-table th, table.products-table td {
            padding: 14px;
            font-size: 14px;
        }
        table.products-table thead {
            background: #e11d48;
            color: white;
        }
        table.products-table th {
            font-weight: 700;
            text-transform: uppercase;
        }
        table.products-table tbody tr {
            border-bottom: 1px solid #eeeeee;
        }
        table.products-table .th-product { width: 50%; text-align: left; }
        table.products-table .th-qty { width: 10%; text-align: center; }
        table.products-table .th-price { width: 20%; text-align: right; }
        table.products-table .th-total { width: 20%; text-align: right; }

        table.products-table .td-product { text-align: left; font-weight: 700; }
        table.products-table .td-qty { text-align: center; font-weight: 700; }
        table.products-table .td-price { text-align: right; font-weight: 700; }
        table.products-table .td-total { text-align: right; font-weight: 700; }

        /* Totals Block */
        .totals-container {
            width: 100%;
        }
        .totals-box {
            width: 320px;
            margin-left: auto;
            text-align: right;
            float: right;
        }
        table.totals-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }
        table.totals-table td {
            padding: 10px 0;
            font-size: 16px;
            font-weight: 700;
            text-align: right;
        }
        table.totals-table .totals-label {
            color: #666;
            width: 50%;
            padding-right: 20px;
        }
        table.totals-table .totals-value {
            color: #111;
            width: 50%;
        }
        table.totals-table tr.total-row td {
            border-top: 3px solid #e11d48;
            padding-top: 20px;
            color: #e11d48;
            font-weight: 900;
            font-size: 18px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-section">
            <tr>
                <td class="title-td">
                    <h1 class="header-title text-red">INVOICE</h1>
                </td>
                <td class="logo-td">
                    <div class="logo">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}">
                        @else
                            <h2 class="text-red" style="margin:0; font-size:40px;">L O G O</h2>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Info -->
        <table class="info-section">
            <tr>
                <td class="info-left">
                    <span class="meta-label">Invoice Number</span>
                    <span class="meta-value">#{{ $invoice->invoice_number }}</span>

                    <span class="meta-label">Date</span>
                    <span class="meta-value">{{ $invoice->created_at->format('M d, Y') }}</span>

                    <span class="meta-label">Payment Method</span>
                    <span class="meta-value">{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
                </td>
                <td class="info-right">
                    <span class="meta-label">Billed To</span>
                    <div class="billed-to-name">{{ $order->customer->name ?? 'Guest User' }}</div>
                    
                    @if(!empty($order->customer->phone))
                        <div class="billed-to-text">{{ $order->customer->phone }}</div>
                    @endif
                    
                    @if(!empty($order->shipping_address))
                        <div class="billed-to-text">{{ $order->shipping_address }}</div>
                    @endif
                    
                    @if(!empty($order->city))
                        <div class="billed-to-text">{{ $order->city }}@if(!empty($order->province)), {{ $order->province }}@endif</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th class="th-product">ITEM</th>
                    <th class="th-qty">QTY</th>
                    <th class="th-price">PRICE</th>
                    <th class="th-total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="td-product">{{ $item->product->name ?? 'Unknown Product' }}</td>
                    <td class="td-qty">{{ $item->quantity }}</td>
                    <td class="td-price">{{ number_format($item->price, 2) }}</td>
                    <td class="td-total">{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Float Wrapper -->
        <div class="totals-container">
            <div class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal:</td>
                        <td class="totals-value">{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">Delivery:</td>
                        <td class="totals-value">
                            @if($order->delivery_price > 0)
                                {{ number_format($order->delivery_price, 2) }}
                            @else
                                <span class="text-red">FREE</span>
                            @endif
                        </td>
                    </tr>
                    @if(isset($order->discount) && $order->discount > 0)
                    <tr>
                        <td class="totals-label text-red">Discount:</td>
                        <td class="totals-value text-red">-{{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="totals-label">FINAL TOTAL:</td>
                        <td class="totals-value">{{ number_format($order->final_total, 2) }}</td>
                    </tr>
                </table>
            </div>
            <!-- Clear floats for PDF container rules padding rendering -->
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
