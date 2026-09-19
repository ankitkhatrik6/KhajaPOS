<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f1f5f9;
            padding: 20px 10px;
            color: #000;
        }
        .receipt {
            max-width: 320px;
            margin: 0 auto;
            background: #fff;
            padding: 16px 14px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            font-size: 12px;
            line-height: 1.4;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
        }
        td {
            padding: 3px 0;
            vertical-align: top;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }
        .actions {
            max-width: 320px;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
        }
        .btn {
            background: #0f172a;
            color: #fff;
            padding: 6px 12px;
            font-size: 11px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        @media print {
            /* 80mm thermal: lock the page to 80mm wide so it never scales to A4/Letter */
            @page {
                size: 80mm 297mm;
                margin: 0;
            }
            html, body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            body {
                background: #fff;
            }
            .receipt {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 2mm;
                width: 80mm;
                max-width: 80mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="actions no-print">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn" style="background:#64748b;">&larr; Back</a>
        <button onclick="openPrintDialog()" class="btn">Print 80mm Receipt</button>
    </div>
    <p class="no-print" style="max-width:320px;margin:-4px auto 12px;font-size:10px;line-height:1.5;color:#64748b;text-align:center;">
        The print dialog lets you choose any printer — a receipt printer, a normal printer or a
        thermal 80&nbsp;mm printer — or <strong>Print to File</strong> to save the receipt as a PDF.
    </p>

    <div class="receipt">
        <!-- Header -->
        <div class="text-center">
            <h2 class="bold" style="font-size:14px; text-transform:uppercase;">{{ $invoice->restaurant_name }}</h2>
            <p>{{ $invoice->restaurant_address }}</p>
            <p>Tel: {{ $invoice->restaurant_phone }}</p>
            @if($invoice->restaurant_pan)
            <p class="bold">PAN: {{ $invoice->restaurant_pan }}</p>
            <p>{{ $invoice->restaurant_vat }}</p>
            @endif
        </div>

        <div class="divider"></div>

        <div class="text-center bold">
            *** TAX INVOICE ***
        </div>

        <div class="divider"></div>

        <div>
            <div class="totals-row">
                <span>Invoice:</span>
                <span class="bold">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="totals-row">
                <span>Date:</span>
                <span>{{ $invoice->created_at->setTimezone('Asia/Kathmandu')->format('Y-m-d h:i A') }}</span>
            </div>
            <div class="totals-row">
                <span>Customer:</span>
                <span>{{ $invoice->customer_name }}</span>
            </div>
            <div class="totals-row">
                <span>Cashier:</span>
                <span>{{ $invoice->cashier_name }}</span>
            </div>
            <div class="totals-row">
                <span>Payment:</span>
                <span class="bold">{{ $invoice->payment_method }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width:50%;">ITEM</th>
                    <th style="width:15%; text-align:center;">QTY</th>
                    <th style="width:15%; text-align:right;">RATE</th>
                    <th style="width:20%; text-align:right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->sale->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right;" class="bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <div>
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>Rs. {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount > 0)
            <div class="totals-row">
                <span>Discount:</span>
                <span>-Rs. {{ number_format($invoice->discount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row">
                <span>VAT (13%):</span>
                <span>Rs. {{ number_format($invoice->tax, 2) }}</span>
            </div>

            <div class="double-divider"></div>

            <div class="totals-row grand-total">
                <span>GRAND TOTAL:</span>
                <span>Rs. {{ number_format($invoice->grand_total, 2) }}</span>
            </div>

            <div class="double-divider"></div>
        </div>

        <!-- Footer -->
        <div class="text-center" style="margin-top:10px; font-size:11px;">
            <p class="bold">{{ $invoice->invoice_footer }}</p>
            <p style="margin-top:4px;">धन्यवाद ! फेरि आउनुहोला !</p>
        </div>
    </div>

    <script>
        // One-click printing: this page raises the system print dialog directly.
        // The dialog lists every printer configured on this Linux machine —
        // receipt printers, normal/office printers and thermal 80 mm printers —
        // plus "Print to File", which saves the receipt as a PDF.
        var openPrintDialog = (function () {
            var triggered = false;
            return function () {
                if (triggered) return;
                triggered = true;
                window.print();
            };
        })();
        @if(request()->query('autoprint'))
        // Opened from a "Print ..." button (?autoprint=1): show the dialog as
        // soon as the page has finished rendering, no second click needed.
        window.addEventListener('load', function () {
            setTimeout(openPrintDialog, 500);
        });
        @endif
    </script>
</body>
</html>
