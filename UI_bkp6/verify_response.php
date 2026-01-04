<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Blocked | Affitrace</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #374151;
        }
        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border-top: 5px solid #ef4444;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #111827;
        }
        p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .spinner {
            margin: 0 auto 1.5rem;
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #ef4444;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        /* ... styles truncated for brevity ... */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .footer {
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="spinner"></div>
        <h1>URL Blocked</h1>
        <p>This URL has been blocked. You will be redirected shortly.</p>
        <div class="footer">Redirecting in <span id="timer">7</span> seconds...</div>
    </div>

    <script>
        let timeLeft = 7;
        const timerElement = document.getElementById('timer');

        const countdown = setInterval(() => {
            timeLeft--;
            timerElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = "https://AffiTrace.com";
            }
        }, 1000);
    </script>
</body>

</html>