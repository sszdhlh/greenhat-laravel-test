<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to ACME</title>
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
            background-color: #2c5aa0;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .employee-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to ACME!</h1>
    </div>
    
    <div class="content">
        <h2>Registration Confirmed</h2>
        
        <p>Dear {{ $employee->user->first_name }},</p>
        
        <p>Thank you for registering with ACME! Your employee registration has been successfully processed.</p>
        
        <div class="employee-info">
            <h3>Your Registration Details:</h3>
            <ul>
                <li><strong>Name:</strong> {{ $employee->user->name }}</li>
                <li><strong>Email:</strong> {{ $employee->user->email }}</li>
                <li><strong>Role:</strong> {{ $employee->role }}</li>
                @if($employee->highest_qualification)
                    <li><strong>Highest Qualification:</strong> {{ $employee->highest_qualification }}</li>
                @endif
                @if($employee->desired_salary)
                    <li><strong>Desired Salary:</strong> ${{ number_format($employee->desired_salary, 2) }}</li>
                @endif
            </ul>
        </div>
        
        <p>Our team will review your application and contact you soon with further instructions.</p>
        
        <p>If you have any questions, please don't hesitate to contact our HR department.</p>
        
        <p>Welcome aboard!</p>
        
        <p>Best regards,<br>
        The ACME Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} ACME. All rights reserved.</p>
    </div>
</body>
</html>
