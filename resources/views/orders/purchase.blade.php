<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Orden de Compra</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0 20px;
        }

        .header {
            position: relative;
            margin-bottom: 15px;
            border-bottom: 3px solid #005d4e;
            overflow: hidden;
            padding: 10px 0;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            height: 70px;
            margin-top: 20px;
            margin-right: 20px;
        }

        .document-info {
            text-align: right;
        }

        .document-title {
            color: #005d4e;
            font-size: 20px;
            margin: 0;
            line-height: 1.2;
        }

        .document-number {
            font-size: 14px;
            margin: 5px 0;
        }

        .document-date {
            font-size: 12px;
            color: #666;
        }

        .client-info {
            margin: 15px 0;
        }

        .info-line {
            margin-bottom: 8px;
        }

        .info-label {
            color: #005d4e;
            font-weight: bold;
            width: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background-color: #005d4e;
            color: white;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-amount {
            font-size: 14px;
            color: #005d4e;
            font-weight: bold;
        }

        .payment-box {
            page-break-inside: avoid;
            border: 1px solid #005d4e;
            margin-top: 30px;
            width: 100%;
        }

        .footer {
            position: absolute;
            bottom: -30px;
            width: 100%;
            text-align: center;
            color: #666;
            font-size: 10px;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('assets/logo.png') }}" class="{{ config('app.company_name') }} logo" alt="Logo">
        <div class="document-info">
            <h1 class="document-title">ORDEN DE COMPRA</h1>
            <div class="document-number">#{{ str_pad($purchase->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="document-date">{{ $purchase->date->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="client-info">
        <div class="info-line">
            <span class="info-label">Cliente:</span>
            {{ $purchase->customer_name }}
        </div>

        <div class="info-line">
            <span class="info-label">Contacto:</span>
            ☎ {{ $purchase->customer_phone }} | ✉ {{ $purchase->customer_mail }}
        </div>

        <div class="info-line">
            <span class="info-label">Dirección:</span>
            <div>{{ $purchase->customer_address }}</div>
            <div>{{ $purchase->customer_state }}, {{ $purchase->customer_city }}, C.P.
                {{ $purchase->customer_zip ?? 'N/A' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60%">Producto</th>
                <th style="width: 10%">Cantidad</th>
                <th style="width: 15%">Precio Unitario</th>
                <th style="width: 15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ $item->unit_price }} MXN</td>
                    <td>${{ $item->subtotal }} MXN</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"></td>
                <td>${{ $purchase->items->sum('subtotal') }} MXN</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div>
            Costo de envío: ${{ $purchase->shipping_fee }} MXN
        </div>
        <div class="total-amount">
            TOTAL: ${{ $purchase->value }} MXN
        </div>
    </div>

    <div class="payment-box">
        <div style="margin: 10px;">
            <h3>CLABE INTERBANCARIA</h3>
            <p>Banco: {{ config('bank.name') }}<br>
                CLABE: {{ config('bank.clabe') }}<br>
                Titular: {{ config('bank.holder_name') }}</p>
        </div>
    </div>

    <footer class="footer">
        Documento válido solo como comprobante de compra - {{ config('app.company_name') }} © {{ date('Y') }}
    </footer>
</body>

</html>
