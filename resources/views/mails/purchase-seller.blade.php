<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo de venta</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "Nunito", sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .header {
            background: #005d4e;
            width: 100%;
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-around;
            text-align: center;
            align-self: center;
            align-items: center;
        }

        .container {
            color: black;
            padding: 10px;
        }

        p {
            text-align: justify;
        }

        .contact-info {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        footer {
            display: flex;
            justify-content: center;
            text-align: center;
            align-self: center;
        }

        strong {
            color: #0496fb;
        }

        .user-name {
            font-size: 13px;
            color: #0496fb;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h4>¡Genial, hemos recibido un pedido!</h4>
    </div>
    <div class="container">
        <p>Se ha registrado una compra realizada por {{ $purchase->customer_name }}.</p>

        <p>Los datos del cliente son: </p>
        <p>Nombre del cliente: {{ $purchase->customer_name }}</p>
        <p>Dirección del cliente: {{ $purchase->customer_address }}</p>
        <p>{{ $purchase->customer_city }} - {{ $purchase->customer_state }}</p>


        <p>Descarga la orden de compra adjunta a este correo y realiza el envío en el menor tiempo posible.</p>

        <p>Cordialmente,</p>

        <div class="contact-info">
            <p>El equipo {{ config('app.company.name') }}</p>
        </div>
    </div>

    <footer>
        <small>Este es un mensaje generado automáticamente. Si tiene alguna duda, contactarse con el delegado.</small>
    </footer>
</body>

</html>
