<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo de factura</title>
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
            background: #5f3713;
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
            color: #5f3713;
        }

        .user-name {
            font-size: 13px;
            color: #5f3713;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h4>¡Tu compra fue realizada con éxito!</h4>
    </div>
    <div class="container">
        <p>Estimado(a) <span class="user-name">{{ $purchase->customer_name }}</span>!</p>

        <p>Muchas gracias por confiar en <strong>{{ config('app.company.name') }}</strong>. Estamos emocionados de que
            puedas experimentar nuestro producto y disfrutar de tus mejores momentos con él.</p>

        <p>Enviaremos a la mayor brevedad tu producto a los siguientes datos:</p>
        <p>Hemos enviado tu pedido a la siguiente dirección:</p>
        <p>{{ $purchase->customer_address }}</p>
        <p>{{ $purchase->customer_city }}, {{ $purchase->customer_state }}</p>


        <p>Quedamos a tu disposición para cualquier consulta que pueda surgir.</p>

        <p>Cordialmente,</p>

        <div class="contact-info">
            <p>El equipo {{ config('app.company.name') }}</p>
        </div>
    </div>

    <footer style="margin-top: 20px; text-align: center; font-size: 12px; color: #555;">
        Elaborado por&nbsp;<a href="https://wa.me/525564828212">Internow Corp</a>.
    </footer>
</body>

</html>
