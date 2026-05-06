<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coming Soon</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
      color: #f0f0f0;
      font-family: system-ui, -apple-system, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .container {
      max-width: 700px;
      padding: 20px;
    }

    h1 {
      font-size: 5.5rem;
      margin: 0.2em 0 0.4em;
      font-weight: 800;
      letter-spacing: -2px;
      background: linear-gradient(90deg, #a78bfa, #7dd3fc);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .subtitle {
      font-size: 1.6rem;
      opacity: 0.9;
      margin: 0 0 2.5rem;
      line-height: 1.4;
    }

    .soon {
      font-size: 2.2rem;
      color: #c4b5fd;
      margin: 2rem 0;
      font-weight: 500;
    }

    .email-form {
      margin-top: 2.5rem;
    }

    input[type="email"] {
      padding: 14px 20px;
      font-size: 1.1rem;
      border: none;
      border-radius: 50px 0 0 50px;
      width: 260px;
      max-width: 80%;
    }

    button {
      padding: 14px 28px;
      font-size: 1.1rem;
      background: #7c3aed;
      color: white;
      border: none;
      border-radius: 0 50px 50px 0;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #6d28d9;
    }

    .footer {
      margin-top: 5rem;
      font-size: 0.95rem;
      opacity: 0.7;
    }
  </style>
</head>
<body>

  <div class="container">
    <!--<h1>COMING SOON</h1>-->
    <p class="subtitle">
      Something awesome is in the works...<br>
      We're getting everything ready for you!
    </p>

    <div class="soon">Stay tuned • Very soon</div>
  </div>

</body>
</html>