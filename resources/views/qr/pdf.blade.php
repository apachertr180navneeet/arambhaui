<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Aarambh QR Voucher Sheet - A4</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 4mm 4mm 5mm 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            font-size: 7px;
            background: #ffffff;
        }

        .header {
            text-align: center;
            padding-bottom: 3px;
            margin-bottom: 3px;
            border-bottom: 0.8px solid #cbd5e1;
        }

        .header .brand-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin: 0 0 1px 0;
        }

        .header .meta-line {
            font-size: 7px;
            color: #475569;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td {
            text-align: center;
            vertical-align: middle;
            border: 0.7px dashed #94a3b8;
            padding: 2px 1px;
            width: {{ 100 / ($cols ?? 10) }}%;
            height: 25mm;
            max-height: 25mm;
            overflow: hidden;
        }

        .qr-card {
            display: block;
            width: 100%;
            text-align: center;
        }

        .qr-brand {
            font-size: 5.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1;
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
        }

        .qr-img {
            width: 44px;
            height: 44px;
            display: block;
            margin: 0 auto;
        }

        .qr-amt {
            font-size: 7px;
            font-weight: 900;
            color: #047857;
            line-height: 1;
            margin-top: 1px;
        }

        .qr-code {
            font-size: 4.8px;
            font-weight: 700;
            color: #1e293b;
            font-family: monospace;
            line-height: 1;
            margin-top: 1px;
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
        }

        .footer {
            text-align: center;
            margin-top: 4px;
            font-size: 6.5px;
            color: #94a3b8;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="brand-title">Aarambh Garments &bull; QR Voucher Sheet</div>
        <div class="meta-line">
            Batch: <strong>{{ $batchName ?? 'All Active Vouchers' }}</strong> &nbsp;|&nbsp; 
            Count: <strong>{{ count($qrs) }} QRs</strong> &nbsp;|&nbsp; 
            Printed: {{ $generatedAt ?? date('d-M-Y H:i') }}
        </div>
    </div>

    @php
        $totalCols = $cols ?? 10;
        $totalRows = ceil(count($qrs) / $totalCols);
    @endphp

    <table>
        @for ($r = 0; $r < $totalRows; $r++)
            <tr>
                @for ($c = 0; $c < $totalCols; $c++)
                    @php
                        $index = $r * $totalCols + $c;
                    @endphp
                    <td>
                        @if (isset($qrs[$index]))
                            @php
                                $item = $qrs[$index];
                                $brandName = !empty($item['batch_name']) ? $item['batch_name'] : (!empty($item['title']) ? $item['title'] : 'AARAMBH');
                                $code = $item['voucher_code'] ?? ($item['qr_id'] ?? '');
                                $amt = $item['amount'] ?? ($item['points'] ?? ($item['discount_amount'] ?? 500));
                                $qrSrc = !empty($item['qr_base64']) 
                                    ? 'data:image/svg+xml;base64,' . $item['qr_base64']
                                    : (!empty($item['qr_image']) ? public_path('images/qrcodes/' . $item['qr_image']) : '');
                            @endphp
                            <div class="qr-card">
                                <div class="qr-brand">{{ \Illuminate\Support\Str::limit($brandName, 12, '') }}</div>
                                @if(!empty($qrSrc))
                                    <img src="{{ $qrSrc }}" class="qr-img" alt="QR">
                                @else
                                    <div style="width:44px; height:44px; border:1px solid #ddd; margin:0 auto; line-height:44px; font-size:5px;">NO QR</div>
                                @endif
                                <div class="qr-amt">₹{{ number_format($amt) }}</div>
                                <div class="qr-code">{{ $code }}</div>
                            </div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>

    <div class="footer">
        {{ config('app.name', 'Aarambh GarmentERP') }} &copy; {{ date('Y') }} &bull; Confidential Single-Use Promotional QR Vouchers
    </div>

</body>
</html>
