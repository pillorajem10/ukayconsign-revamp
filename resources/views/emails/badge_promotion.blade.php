<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge Promotion</title>
</head>
<body>
    <h1>Congratulations, {{ $user->name }}!</h1>

    <p>We are thrilled to inform you that you have been promoted to <strong>{{ $user->badge }}</strong> badge!</p>

    @if ($user->badge === 'Gold')
        <p>You can now order a maximum of 5 bundles.</p>
    @elseif ($user->badge === 'Platinum')
        <p>You can now order unlimited bundles.</p>
    @endif

    <p>Thank you for being a valued member of our community. Your hard work and dedication are greatly appreciated.</p>

    <p>Best regards,<br>USC Team</p>
</body>
</html>
