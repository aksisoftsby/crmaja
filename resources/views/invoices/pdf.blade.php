<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; }
        h1 { color: #0f172a; font-size: 21px; margin: 0 0 4px; }
        h2 { font-size: 12px; margin: 0 0 6px; }
        .muted { color: #6b7280; }
        .header, .details { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; width: 50%; }
        .details { margin: 24px 0; }
        .details td { width: 50%; vertical-align: top; padding: 12px; background: #f8fafc; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .items th { background: #0f172a; color: #fff; text-align: left; font-size: 9px; padding: 9px; }
        .items td { border-bottom: 1px solid #e5e7eb; padding: 9px; }
        .right { text-align: right; }
        .totals { width: 42%; margin-left: auto; border-collapse: collapse; margin-top: 16px; }
        .totals td { padding: 6px 0; }
        .totals tr:last-child td { border-top: 1px solid #94a3b8; font-size: 12px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; color: #6b7280; font-size: 8px; }
    </style>
</head>
<body>
    <table class="header"><tr><td><h1>Aksisoft CRM</h1><span class="muted">Dokumen tagihan</span></td><td class="right"><h2>INVOICE</h2><strong>{{ $invoice->number }}</strong><br><span class="muted">Tanggal: {{ $invoice->date->format('d M Y') }}</span><br><span class="muted">Jatuh tempo: {{ $invoice->due_date?->format('d M Y') ?? '—' }}</span></td></tr></table>

    <table class="details"><tr><td><h2>Ditagihkan kepada</h2><strong>{{ $invoice->client->company_name }}</strong><br>@if($invoice->client->vat_number)<span class="muted">NPWP: {{ $invoice->client->vat_number }}</span>@endif</td><td><h2>Status pembayaran</h2><strong>{{ ucfirst($invoice->status) }}</strong><br><span class="muted">Total dibayar: Rp {{ number_format((float) $invoice->paid_amount, 2, ',', '.') }}</span></td></tr></table>

    <table class="items"><thead><tr><th>Deskripsi</th><th class="right">Qty</th><th class="right">Tarif</th><th class="right">Jumlah</th></tr></thead><tbody>@foreach($invoice->items as $item)<tr><td>{{ $item->description }}</td><td class="right">{{ number_format((float) $item->qty, 2, ',', '.') }}</td><td class="right">Rp {{ number_format((float) $item->rate, 2, ',', '.') }}</td><td class="right">Rp {{ number_format((float) $item->amount, 2, ',', '.') }}</td></tr>@endforeach</tbody></table>

    <table class="totals"><tr><td>Subtotal</td><td class="right">Rp {{ number_format((float) $invoice->subtotal, 2, ',', '.') }}</td></tr><tr><td>Diskon</td><td class="right">Rp {{ number_format((float) $invoice->discount, 2, ',', '.') }}</td></tr><tr><td>Total</td><td class="right">Rp {{ number_format((float) $invoice->total, 2, ',', '.') }}</td></tr></table>

    @if($invoice->notes)<p><strong>Catatan:</strong><br>{{ $invoice->notes }}</p>@endif
    <div class="footer">Dibuat oleh Aksisoft CRM pada {{ now()->format('d M Y H:i') }} WIB.</div>
</body>
</html>
