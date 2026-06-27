<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceData['invoice_number'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background:
                {{ $branding['primary_color'] }}
            ;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .logo {
            max-height: 50px;
            margin-bottom: 12px;
        }

        .email-body {
            padding: 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .invoice-details td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .invoice-details td:first-child {
            font-weight: 600;
            color: #555;
            width: 40%;
        }

        .total-row td {
            border-bottom: none;
            border-top: 2px solid
                {{ $branding['primary_color'] }}
            ;
            font-size: 18px;
            font-weight: 700;
            color:
                {{ $branding['primary_color'] }}
            ;
            padding-top: 15px;
        }

        .note {
            font-size: 13px;
            color: #777;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .email-footer {
            background: #f8f9fc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            @if($branding['platform_logo'])
                <img src="{{ $branding['platform_logo'] }}" class="logo" alt="{{ $branding['platform_name'] }}">
            @endif
            <h1>Invoice {{ $invoiceData['invoice_number'] }}</h1>
            <p>{{ $invoiceData['title'] ?? 'Invoice' }}</p>
        </div>

        <div class="email-body">
            <p class="greeting">Dear {{ $invoiceData['advertiser_name'] }},</p>
            <p>Please find your invoice details below. The invoice PDF is attached to this email.</p>

            <table class="invoice-details">
                <tr>
                    <td>Invoice Number</td>
                    <td>{{ $invoiceData['invoice_number'] }}</td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td>{{ $invoiceData['invoice_date'] }}</td>
                </tr>
                <tr>
                    <td>Due Date</td>
                    <td>{{ $invoiceData['due_date'] ?? 'N/A' }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td>${{ $invoiceData['total_amount'] }}</td>
                </tr>
            </table>

            <p class="note">
                If you have any questions about this invoice, please don't hesitate to contact us.
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }}
                {{ $branding['custom_copyright'] ?? ($branding['platform_name'] . '. All rights reserved.') }}</p>
        </div>
    </div>
</body>

</html>