<!-- resources/views/emails/demoEmail.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['title'] }}</title>
</head>
<body>
    <p>{{ $details['body'] }}</p>

    <a href="{{$details['reset_url']}}">Cambia tu contraseña aquí</a>
</body>
</html>
