<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset - Etchon Water Rides</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌊 Etchon Water Rides</h1>
        <h2>Password Reset Request</h2>
    </div>
    
    <div class="content">
        <p>Hi {{ $user->name }},</p>
        
        <p>We received a request to reset your password for your Etchon Water Rides account.</p>
        
        <p>Click the button below to reset your password:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 4px; font-family: monospace;">
            {{ $resetUrl }}
        </p>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>This link will expire in 10 minutes for security reasons</li>
            <li>If you didn't request this password reset, please ignore this email</li>
            <li>Your password will remain unchanged until you create a new one</li>
        </ul>
        
        <p>If you're having trouble, please contact our support team.</p>
        
        <p>Thanks,<br>The Etchon Water Rides Team</p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $user->email }} because a password reset was requested for your account.</p>
    </div>
</body>
</html>
