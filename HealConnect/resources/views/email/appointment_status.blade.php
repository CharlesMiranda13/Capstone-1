<!DOCTYPE html>
<html>
<head>
    <title>Appointment Status</title>
</head>
<body>
    <p>Hello {{ $appointment->patient->name }},</p>

    <p>Your appointment with {{ $appointment->provider->name }} on 
       {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }} at 
       {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }} 
       has been <strong>{{ ucfirst($appointment->status) }}</strong>.</p>

    <p>Thank you for using our service!</p>
</body>
</html>
