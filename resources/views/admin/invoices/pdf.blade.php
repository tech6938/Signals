<!DOCTYPE html>
<html>

<head>
  <title>Invoice</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      color: #0f172a;
      font-size: 12px;
    }

    .text-right {
      text-align: right;
    }

    .text-left {
      text-align: left;
    }

    .text-center {
      text-align: center;
    }

    .muted {
      color: #64748b;
    }

    .h1 {
      font-size: 22px;
      font-weight: bold;
      letter-spacing: 0.5px;
    }

    .h2 {
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
      color: #475569;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 8px;
      border: 1px solid #e2e8f0;
    }

    thead th {
      background: #f1f5f9;
      font-weight: bold;
      color: #334155;
    }

    .no-border {
      border: 0 !important;
    }

    .header-band {
      background: #0ea5e9;
      color: #fff;
    }

    .header-band td {
      border-color: #0ea5e9;
      padding: 14px 12px;
    }

    .brand {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .invoice-tag {
      background: #0369a1;
      color: #e0f2fe;
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
      font-size: 11px;
    }

    .meta td {
      border: 0;
      padding: 2px 0;
    }

    .card {
      border: 1px solid #e2e8f0;
    }

    .totals td {
      border-left: 0;
      border-right: 0;
    }

    .totals .label {
      text-align: right;
      background: #f8fafc;
      color: #334155;
    }

    .totals .amount {
      width: 160px;
      text-align: right;
      font-weight: 700;
      color: #111827;
    }

    .grand {
      background: #e0f2fe;
      font-weight: 800;
    }

    .footer {
      margin-top: 18px;
      font-size: 11px;
      color: #64748b;
      border-top: 1px solid #e2e8f0;
      padding-top: 8px;
    }
  </style>
</head>

<body>
  <table class="header-band">
    <tr>
      <td style="width:60%;">
        <div class="brand">YOUR COMPANY</div>
        <div class="muted" style="color:#e0f2fe;">Address line 1, City</div>
        <div class="muted" style="color:#e0f2fe;">support@example.com · +1 234 567 890</div>
      </td>
      <td class="text-right" style="width:40%;">
        <div class="invoice-tag">Invoice #{{ $invoice->id }}</div>
        <div>Issued: {{ optional($invoice->created_at)->format('Y-m-d') }}</div>
      </td>
    </tr>
  </table>

  <table class="card" style="margin-top:12px;">
    <tr>
      <td style="width:50%; vertical-align:top;">
        <div class="h2">Bill To</div>
        <div style="margin:6px 0 2px 0;"><strong>{{ $invoice->name }}</strong></div>
        <div class="muted">{{ $invoice->phone }}</div>
        <div class="muted">{{ $invoice->currency }}</div>
      </td>
      <td style="width:50%; vertical-align:top;">
        <div class="h2">Invoice Details</div>
        <table class="no-border meta" style="margin-top:6px;">
          <tr>
            <td class="no-border muted" style="width:140px;">Package Type</td>
            <td class="no-border"><strong>{{ $invoice->package?->name ?? 'N/A' }}</strong>
            </td>
          </tr>
          <tr>
            <td class="no-border muted">Start Date</td>
            <td class="no-border">{{ \Carbon\Carbon::parse($invoice->start_date)->format('Y-m-d') }}</td>
          </tr>
          <tr>
            <td class="no-border muted">End Date</td>
            <td class="no-border">{{ \Carbon\Carbon::parse($invoice->end_date)->format('Y-m-d') }}</td>
          </tr>
          <tr>
            <td class="no-border muted">Duration</td>
            <td class="no-border">{{ $invoice->duration }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th style="width:60%;">Description</th>
        <th style="width:20%;" class="text-right">Rate</th>
        <th style="width:20%;" class="text-right">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {{ $invoice->service_type }}
          <div class="muted">{{ \Carbon\Carbon::parse($invoice->start_date)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($invoice->end_date)->format('Y-m-d') }}</div>
        </td>
        <td class="text-right">${{ number_format($invoice->amount, 2) }}</td>
        <td class="text-right">${{ number_format($invoice->amount, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <table class="no-border totals" style="border:0; margin-top:10px;">
    <tr class="no-border">
      <td class="no-border" style="width:60%;"></td>
      <td class="no-border label">Subtotal</td>
      <td class="no-border amount">${{ number_format($invoice->amount, 2) }}</td>
    </tr>
    <tr class="no-border grand">
      <td class="no-border"></td>
      <td class="no-border label">Total</td>
      <td class="no-border amount">${{ number_format($invoice->amount, 2) }}</td>
    </tr>
  </table>

  <div class="footer">
    Thank you for your business. Payment due upon receipt. If you have any questions about this invoice, please contact us at support@example.com.
  </div>
</body>

</html>