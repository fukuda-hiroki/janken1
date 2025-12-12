<?php
// PHPファイルをUTF-8で保存していることを前提とします

// 1. 定義
const JANKEN_HANDS = [
    'rock' => 'グー',
    'scissors' => 'チョキ',
    'paper' => 'パー',
];

// 2. 処理
$user_hand = $_GET['hand'] ?? null;
$computer_hand = array_rand(JANKEN_HANDS);
$result = '';
$message = '下のボタンから手を選んでください。';

if (isset(JANKEN_HANDS[$user_hand])) {
    $user_hand_text = JANKEN_HANDS[$user_hand];
    $computer_hand_text = JANKEN_HANDS[$computer_hand];

    // 勝敗判定ロジック
    if ($user_hand === $computer_hand) {
        $result = '引き分け';
    } elseif (
        ($user_hand === 'rock' && $computer_hand === 'scissors') ||
        ($user_hand === 'scissors' && $computer_hand === 'paper') ||
        ($user_hand === 'paper' && $computer_hand === 'rock')
    ) {
        $result = 'あなたの**勝ち**です！';
    } else {
        $result = 'コンピュータの**勝ち**です...';
    }

    $message = "あなたは【{$user_hand_text}】、コンピュータは【{$computer_hand_text}】を出しました。";
}

// 3. 表示 (HTML出力)
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP じゃんけんゲーム</title>
    <style>
        body { font-family: 'Arial', sans-serif; text-align: center; padding-top: 50px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .result-box { margin: 20px 0; padding: 15px; background-color: #f0f0f0; border-radius: 5px; }
        .hand-button { padding: 10px 20px; font-size: 16px; margin: 5px; cursor: pointer; border: none; border-radius: 5px; }
        .rock { background-color: #ffcccc; }
        .scissors { background-color: #ccffcc; }
        .paper { background-color: #ccccff; }
        strong { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✊ じゃんけんゲーム ✋</h1>

        <div class="result-box">
            <?php if ($result): ?>
                <h2>結果: <?php echo $result; ?></h2>
                <p><?php echo $message; ?></p>
                <hr>
                <p>もう一度勝負しますか？</p>
            <?php else: ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </div>

        <form method="GET" action="janken.php">
            <?php foreach (JANKEN_HANDS as $hand_key => $hand_text): ?>
                <button type="submit" name="hand" value="<?php echo $hand_key; ?>" class="hand-button <?php echo $hand_key; ?>">
                    <?php echo $hand_text; ?>
                </button>
            <?php endforeach; ?>
        </form>
    </div>
</body>
</html>
