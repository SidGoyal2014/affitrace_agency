<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        body  { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
        .wrap { max-width:600px; margin:40px auto; background:#fff; border-radius:6px; overflow:hidden; }
        .hdr  { background:#2563eb; color:#fff; padding:24px 32px; }
        .hdr h1 { margin:0; font-size:20px; }
        .body { padding:32px; color:#374151; line-height:1.6; }
        .ftr  { background:#f9fafb; padding:16px 32px; font-size:12px; color:#9ca3af; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="hdr">
        <h1>{{ $notification->title }}</h1>
    </div>
    <div class="body">
        <p>{!! nl2br(e($notification->message)) !!}</p>
    </div>
    <div class="ftr">
        This is an automated notification. Please do not reply to this email.
    </div>
</div>
</body>
</html>
