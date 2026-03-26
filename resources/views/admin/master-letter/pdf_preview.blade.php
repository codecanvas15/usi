<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $model->document_name }}</title>
    <style>
        table {
            width: 100% !important;
        }

        figure {
            margin: 0 !important;
        }
    </style>
</head>

<body>
    {!! $model->template !!}
</body>

</html>
