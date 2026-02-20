<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #111;
            letter-spacing: -1px;
            text-decoration: none;
        }
        .logo span {
            color: #e10600;
        }
        h1 {
            color: #111;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background-color: #e10600;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .highlight {
            color: #e10600;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#" class="logo">THINKER<span>.</span></a>
        </div>
        
        <h1>Hello {{ $invoice->order->customer->name }},</h1>
        
        <p>Thank you for your recent order with <span class="highlight">Thinker Electronic</span>. Your payment has been processed successfully.</p>
        
        <p>Attached to this email, you will find your official invoice <span class="highlight">#{{ $invoice->invoice_number }}</span> for your records.</p>
        
        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <p style="margin: 0;"><strong>Order Reference:</strong> {{ $invoice->order->order_number }}</p>
            <p style="margin: 0;"><strong>Total Amount:</strong> {{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</p>
        </div>

        <p>If you have any questions regarding your order, please do not hesitate to contact our support team.</p>
        
        <a href="{{ env('APP_URL') }}/shop" class="btn">Continue Shopping</a>
        
        <div class="footer">
            <p style="font-weight: 700; color: #111;">تفكر ذكي.. أنظمة ذكية</p>
            <p>&copy; {{ date('Y') }} Thinker Electronic. All rights reserved.</p>
            <p>Innovation in Tech | Smart Systems</p>
        </div>
    </div>
</body>
</html>
