<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $broadcast->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            border-bottom: 2px solid #16a34a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #16a34a;
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $broadcast->title }}</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $recipientName }},</p>
            
            <p>{!! nl2br($broadcast->message) !!}</p>
            
            <p>
                <a href="{{ url('/recipient/dashboard') }}" class="button">View in App</a>
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from the eVoucher Platform. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
