<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ゲーム一覧</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            text-align: center;
            padding-top: 50px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            font-size: 1.2em;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>ゲーム一覧</h1>
        <ul>
            <li>
                <a href="./tictactoe.php">3木並べ</a>
                <a href="./janken.php">じゃんけんゲーム</a>
            </li>
        </ul>
    </div>
</body>

</html>