<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['subject'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8fafc; padding: 30px; color: #334155; }
        .wrapper { background: #ffffff; padding: 24px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff !important; padding: 12px 24px; font-weight: bold; text-decoration: none; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Hello, {{ $data['user_name'] }}!</h2>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <a href="{{ $data['reset_link'] }}" class="btn">Reset Password</a>
        
        <p>If you did not request a password reset, no further action is required.</p>
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <small style="color: #64748b;">If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>{{ $data['reset_link'] }}</small>
    </div>
</body>
</html>