<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Aarambh QR Voucher Sheet - A4</title>
    @php
        $cols = $cols ?? 5;
        if ($cols < 3 || $cols > 10) $cols = 5;
        $cellWidth = number_format(100 / $cols, 2, '.', '');
        $cellHeight = $cols <= 3 ? '52mm' : ($cols <= 4 ? '44mm' : ($cols <= 5 ? '38mm' : ($cols <= 6 ? '32mm' : '26mm')));
        $qrSize = $cols <= 3 ? '32mm' : ($cols <= 4 ? '26mm' : ($cols <= 5 ? '23mm' : ($cols <= 6 ? '19mm' : '15mm')));
        $brandSize = $cols <= 3 ? '10pt' : ($cols <= 4 ? '8.5pt' : ($cols <= 5 ? '7.5pt' : ($cols <= 6 ? '6.5pt' : '5.5pt')));
        $amtSize = $cols <= 3 ? '13pt' : ($cols <= 4 ? '11pt' : ($cols <= 5 ? '9.5pt' : ($cols <= 6 ? '8pt' : '7pt')));
        $codeSize = $cols <= 3 ? '9pt' : ($cols <= 4 ? '7.5pt' : ($cols <= 5 ? '6.5pt' : ($cols <= 6 ? '5.5pt' : '4.8pt')));
        $charLimit = $cols <= 3 ? 30 : ($cols <= 4 ? 24 : ($cols <= 5 ? 18 : 12));
    @endphp
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 5mm 6mm 5mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            font-size: 8pt;
            background: #ffffff;
        }

        .header {
            text-align: center;
            padding-bottom: 4px;
            margin-bottom: 4px;
            border-bottom: 0.8px solid #cbd5e1;
        }

        .header .brand-title {
            font-size: 12pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .header .meta-line {
            font-size: 7.5pt;
            color: #475569;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 auto;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        td {
            text-align: center;
            vertical-align: middle;
            border: 0.7px dashed #94a3b8;
            padding: 2.5mm 1.5mm;
            width: {{ $cellWidth }}%;
            height: {{ $cellHeight }};
            max-height: {{ $cellHeight }};
            box-sizing: border-box;
            overflow: hidden;
        }

        .qr-card {
            display: block;
            width: 100%;
            text-align: center;
            margin: 0 auto;
        }

        .qr-brand {
            font-size: {{ $brandSize }};
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
            margin: 0 0 1.5mm 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 98%;
        }

        .qr-code-box {
            width: {{ $qrSize }};
            height: {{ $qrSize }};
            margin: 0 auto;
            display: block;
            text-align: center;
            overflow: hidden;
        }

        .qr-code-box svg,
        .qr-code-box img,
        .qr-img {
            width: {{ $qrSize }} !important;
            height: {{ $qrSize }} !important;
            max-width: {{ $qrSize }} !important;
            max-height: {{ $qrSize }} !important;
            display: block !important;
            margin: 0 auto !important;
            aspect-ratio: 1 / 1 !important;
            object-fit: contain !important;
        }

        .qr-amt {
            font-size: {{ $amtSize }};
            font-weight: 900;
            color: #047857;
            line-height: 1;
            margin: 1.5mm 0 1mm 0;
        }

        .qr-code {
            font-size: {{ $codeSize }};
            font-weight: 700;
            color: #1e293b;
            font-family: monospace;
            line-height: 1;
            letter-spacing: 0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 98%;
        }

        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 3px;
        }

        @media screen {
            body {
                background: #334155;
                padding: 24px 12px;
                min-height: 100vh;
            }
            .sheet-container {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #ffffff;
                padding: 6mm 5mm 6mm 5mm;
                box-shadow: 0 10px 30px rgba(0,0,0,0.35);
                box-sizing: border-box;
                border-radius: 4px;
            }
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 5mm 6mm 5mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .sheet-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }

        .print-bar {
            background: #1e293b;
            color: #ffffff;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            max-width: 210mm;
            margin: 0 auto 16px auto;
            border-radius: 6px;
        }

        .print-btn {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #1d4ed8;
        }

        .back-btn {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .cols-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            color: #94a3b8;
            background: #334155;
            border: 1px solid #475569;
            transition: all 0.15s;
        }

        .cols-pill:hover,
        .cols-pill.active {
            color: #ffffff;
            background: #4f46e5;
            border-color: #6366f1;
        }
    </style>
</head>
<body>

    @if(!empty($isPrintFallback) || request()->boolean('preview'))
    <div class="no-print print-bar">
        <div>
            <strong>A4 Voucher Sheet Preview</strong> &bull; {{ count($qrs) }} Vouchers &bull; {{ $cols }} Columns
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:11px; color:#94a3b8;">Columns:</span>
            <a href="{{ request()->fullUrlWithQuery(['cols' => 3]) }}" class="cols-pill {{ $cols == 3 ? 'active' : '' }}" title="3 Columns (Extra Large 2.5 inch Stickers)">3</a>
            <a href="{{ request()->fullUrlWithQuery(['cols' => 4]) }}" class="cols-pill {{ $cols == 4 ? 'active' : '' }}" title="4 Columns (Large 2 inch Stickers - Perfect for 12 QRs)">4</a>
            <a href="{{ request()->fullUrlWithQuery(['cols' => 5]) }}" class="cols-pill {{ $cols == 5 ? 'active' : '' }}" title="5 Columns (Standard 1.5 inch Stickers)">5</a>
            <a href="{{ request()->fullUrlWithQuery(['cols' => 6]) }}" class="cols-pill {{ $cols == 6 ? 'active' : '' }}" title="6 Columns (Compact 1.25 inch Stickers)">6</a>

            <a href="javascript:history.back()" class="back-btn" style="margin-left:8px;">&larr; Back</a>
            <button onclick="window.print()" class="print-btn">🖨️ Print / Save as PDF</button>
        </div>
    </div>
    @if(!empty($isPrintFallback))
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 400);
        });
    </script>
    @endif
    @endif

    <div class="sheet-container">
        <div class="header">
            <div class="brand-title">Aarambh Garments &bull; QR Voucher Sheet</div>
            <div class="meta-line">
                Batch: <strong>{{ $batchName ?? 'All Active Vouchers' }}</strong> &nbsp;|&nbsp; 
                Count: <strong>{{ count($qrs) }} QRs</strong> &nbsp;|&nbsp; 
                Layout: <strong>{{ $cols }} Columns</strong> &nbsp;|&nbsp;
                Printed: {{ $generatedAt ?? date('d-M-Y H:i') }}
            </div>
        </div>

        @php
            $totalCols = $cols;
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
                                    $claimUrl = $item['claim_url'] ?? url('/claim/' . $code);
                                @endphp
                                <div class="qr-card">
                                    <div class="qr-brand">{{ \Illuminate\Support\Str::limit($brandName, $charLimit, '') }}</div>
                                    <div class="qr-code-box">
                                        @if(!empty($item['svg_clean']) && stripos($item['svg_clean'], '<svg') !== false)
                                            {!! $item['svg_clean'] !!}
                                        @elseif(!empty($item['qr_base64']))
                                            <img src="data:image/svg+xml;base64,{{ $item['qr_base64'] }}" class="qr-img" alt="QR" onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=0&data={{ urlencode($claimUrl) }}';">
                                        @else
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=0&data={{ urlencode($claimUrl) }}" class="qr-img" alt="QR">
                                        @endif
                                    </div>
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
    </div>

</body>
</html>
