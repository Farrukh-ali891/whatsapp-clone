<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['subject'] ?? 'Notification' }}</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <!-- Accessing the data array passed from your MailSender class -->
        <h1>{{ $data['user_name'] ?? 'Hello!' }}</h1>
        <p>{{ $data['title'] ?? '' }}  {{$data['otp'] ?? ''}}</p>
    </div>
</body>
</html>