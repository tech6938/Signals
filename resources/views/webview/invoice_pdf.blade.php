<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details, .customer-details {
            width: 45%;
        }
        .invoice-details h3, .customer-details h3 {
            color: #007bff;
            margin-top: 0;
        }
        .package-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .package-details h3 {
            color: #007bff;
            margin-top: 0;
        }
        .bank-details {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .bank-details h3 {
            color: #28a745;
            margin-top: 0;
        }
        .total-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .total-section h2 {
            color: #856404;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #{{ $invoice->id }}</p>
    </div>

    <div class="invoice-info">
        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <p><strong>Invoice ID:</strong> {{ $invoice->id }}</p>
            <p><strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
            <p><strong>Start Date:</strong> {{ $invoice->start_date }}</p>
            <p><strong>End Date:</strong> {{ $invoice->end_date }}</p>
            <p><strong>Duration:</strong> {{ $invoice->duration }}</p>
        </div>
        
        <div class="customer-details">
            <h3>Customer Details</h3>
            <p><strong>Name:</strong> {{ $invoice->name }}</p>
            <p><strong>Phone:</strong> {{ $invoice->phone }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <div class="package-details">
        <h3>Package Details</h3>
        <table>
            <tr>
                <th>Package Name</th>
                <td>{{ $package->name }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $package->description }}</td>
            </tr>
            <tr>
                <th>Duration</th>
                <td>{{ $package->duration_days }} days</td>
            </tr>
            <tr>
                <th>Signal Limit</th>
                <td>{{ $package->signal_limit }}</td>
            </tr>
        </table>
    </div>

    <div class="bank-details">
        <h3>Payment Details</h3>
        <table>
            <tr>
                <th>Bank Name</th>
                <td>{{ $bank->bank_name }}</td>
            </tr>
            <tr>
                <th>Account Title</th>
                <td>{{ $bank->account_title }}</td>
            </tr>
            @if($bank->account_number)
            <tr>
                <th>Account Number</th>
                <td>{{ $bank->account_number }}</td>
            </tr>
            @endif
            @if($bank->iban)
            <tr>
                <th>IBAN</th>
                <td>{{ $bank->iban }}</td>
            </tr>
            @endif
            @if($bank->swift_code)
            <tr>
                <th>Swift Code</th>
                <td>{{ $bank->swift_code }}</td>
            </tr>
            @endif
        </table>
        @if($bank->description)
        <p><strong>Note:</strong> {{ $bank->description }}</p>
        @endif
    </div>

    <div class="total-section">
        <h2>Total Amount: {{ strtoupper($invoice->currency) }} {{ $invoice->amount }}</h2>
        <p>Please make payment to the bank details above and keep this invoice as proof of purchase.</p>
    </div>

    <div class="footer">
        <p>This invoice was generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
