<!-- resources/views/emails/user-creation.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Created Successfully</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .logo {
            max-height: 60px;
            margin-bottom: 15px;
        }
        
        .content {
            padding: 25px 20px;
        }
        
        h1 {
            color: #2d3748;
            font-size: 24px;
            margin-top: 0;
            font-weight: 600;
        }
        
        .card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4299e1;
        }
        
        .credentials {
            margin: 15px 0;
        }
        
        .label {
            font-weight: 500;
            color: #4a5568;
            display: inline-block;
            width: 100px;
        }
        
        .value {
            font-weight: 400;
            color: #2d3748;
        }
        
        .password {
            font-size: 18px;
            font-weight: 600;
            color: #2b6cb0;
            letter-spacing: 1px;
            background: #ebf8ff;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        
        .button:hover {
            background-color: #3182ce;
        }
        
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        
        .note {
            font-size: 14px;
            color: #718096;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Replace with your actual logo -->
            <img src="{{ asset('logo.png') }}" alt="Company Logo" class="logo">
            <h1>Your {{ ucfirst($type) }} Account Has Been Created</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $model->name }},</p>
            <p>Welcome to our platform! Your {{ $type }} account has been successfully created.</p>
            
            <div class="card">
                <h3>Account Credentials</h3>
                <div class="credentials">
                    <div>
                        <span class="label">Email:</span>
                        <span class="value">{{ $model->email }}</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <span class="label">Password:</span>
                        <div class="password">{{ $password }}</div>
                    </div>
                </div>
            </div>
            
            <p>Please keep your credentials secure and do not share them with anyone.</p>
            
            <div style="text-align: center;">
                <a href="{{ route($type.'.login') }}" class="button">Login to Your Account</a>
            </div>
            
            <p class="note">Note: For security reasons, we recommend changing your password after your first login.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>If you did not request this account, please contact our support team immediately.</p>
        </div>
    </div>
</body>
</html>