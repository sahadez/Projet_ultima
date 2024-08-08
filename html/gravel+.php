<!doctype html>
<html lang="en">

<head>
    <title>Ultima Mobility</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            color: #fff;
            text-align: center;
            overflow: hidden;
        }

        .coming-soon {
            max-width: 600px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            z-index: 10;
        }

        .coming-soon h1 {
            font-size: 4em;
            margin-bottom: 0.5em;
            font-weight: 700;
        }

        .coming-soon p {
            font-size: 1.2em;
            margin-bottom: 1.5em;
        }

        .coming-soon .btn {
            padding: 10px 20px;
            font-size: 1em;
            font-weight: 700;
            border-radius: 50px;
        }

        .emoji {
            position: absolute;
            top: -50px;
            font-size: 2em;
            animation: fall linear infinite;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh);
            }
        }
    </style>
</head>

<body>
    <div class="coming-soon">
        <h1>Coming Soon</h1>
        <p>We're working hard to finish the development of this site. Stay tuned for something amazing!</p>
        <a href="home.php" class="btn btn-primary">Go Back</a>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script>
        function createFallingEmoji() {
            const emoji = document.createElement('div');
            emoji.classList.add('emoji');
            emoji.textContent = '😊';
            emoji.style.left = `${Math.random() * 100}vw`;
            emoji.style.animationDuration = `${Math.random() * 2 + 3}s`;
            document.body.appendChild(emoji);

            setTimeout(() => {
                emoji.remove();
            }, 5000);
        }

        setInterval(createFallingEmoji, 300);
    </script>
</body>

</html>
