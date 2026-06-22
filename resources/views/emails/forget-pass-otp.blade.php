<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body>
<h2>Password Reset Request</h2>

<p>Your OTP code is:</p>

<h1 style="letter-spacing: 5px; color: #2d3748;">
    {{ $otp }}
</h1>

<p>This code will expire in 5 minutes.</p>

<p>If you did not request this, ignore this email.</p>
</body>
</html>
