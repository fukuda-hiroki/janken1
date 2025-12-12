<?php
// セッションを開始してゲームの状態を保存・取得できるようにする
session_start();

// ----------------------------------------------------
// I. 定数と初期設定
// ----------------------------------------------------
const BOARD_SIZE = 3;
const PLAYER_X = 'X';
const PLAYER_O = 'O';
const CELL_EMPTY = '';

// ----------------------------------------------------
// II. ゲーム状態の初期化・取得
// ----------------------------------------------------
if (!isset($_SESSION['board']) || isset($_GET['reset'])) {
    // 盤面を初期化（3x3の配列をNULLで埋める）
    $_SESSION['board'] = array_fill(0, BOARD_SIZE, array_fill(0, BOARD_SIZE, CELL_EMPTY));
    // 最初のプレイヤーはXとする
    $_SESSION['current_player'] = PLAYER_X;
    // ゲームは進行中とする
    $_SESSION['game_over'] = false;
    // メッセージを初期化
    $_SESSION['message'] = 'ゲーム開始！あなたが【X】です。';
    if (isset($_GET['reset'])) {
        header('Location: tictactoe.php'); // リセット後のクエリパラメータを削除
        exit;
    }
}

$board = &$_SESSION['board'];
$current_player = &$_SESSION['current_player'];
$game_over = &$_SESSION['game_over'];
$message = &$_SESSION['message'];

// ----------------------------------------------------
// III. ロジック関数
// ----------------------------------------------------

/**
 * 勝者を確認する
 * @return string|null 勝者のシンボル ('X' or 'O') または NULL
 */
function check_winner($board)
{
    // 勝利条件のチェック（行、列、対角線）
    $lines = [];

    // 1. 行と列のチェック
    for ($i = 0; $i < BOARD_SIZE; $i++) {
        $lines[] = $board[$i]; // 行
        $lines[] = array_column($board, $i); // 列
    }

    // 2. 対角線のチェック
    $diag1 = []; // 左上から右下
    $diag2 = []; // 右上から左下
    for ($i = 0; $i < BOARD_SIZE; $i++) {
        $diag1[] = $board[$i][$i];
        $diag2[] = $board[$i][BOARD_SIZE - 1 - $i];
    }
    $lines[] = $diag1;
    $lines[] = $diag2;

    foreach ($lines as $line) {
        if (count(array_unique($line)) === 1 && $line[0] !== CELL_EMPTY) {
            return $line[0]; // 勝利者
        }
    }
    return null;
}

/**
 * 盤面が埋まっているか確認する
 * @return bool 埋まっていれば true
 */
function is_board_full($board)
{
    foreach ($board as $row) {
        if (in_array(CELL_EMPTY, $row, true)) {
            return false;
        }
    }
    return true;
}

/**
 * コンピュータ (O) の手番
 */
function computer_move(&$board, &$current_player, &$message)
{
    // シンプルなランダム配置ロジック
    $empty_cells = [];
    for ($r = 0; $r < BOARD_SIZE; $r++) {
        for ($c = 0; $c < BOARD_SIZE; $c++) {
            if ($board[$r][$c] === CELL_EMPTY) {
                $empty_cells[] = ['row' => $r, 'col' => $c];
            }
        }
    }

    if (!empty($empty_cells)) {
        $move = $empty_cells[array_rand($empty_cells)];
        $board[$move['row']][$move['col']] = PLAYER_O;
        $message = 'コンピュータが手番を終えました。あなたの番です。';
        $current_player = PLAYER_X;
    }
}

// ----------------------------------------------------
// IV. ユーザー入力処理 (ゲームプレイ)
// ----------------------------------------------------

if (!$game_over && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move'])) {
    $parts = explode('_', $_POST['move']);
    $row = (int) $parts[0];
    $col = (int) $parts[1];

    if ($board[$row][$col] === CELL_EMPTY && $current_player === PLAYER_X) {
        // 1. ユーザー (X) の手番処理
        $board[$row][$col] = PLAYER_X;

        // 2. 勝敗判定
        $winner = check_winner($board);

        if ($winner) {
            $game_over = true;
            $message = "👑 【{$winner}】の**勝利**です！おめでとうございます！";
        } elseif (is_board_full($board)) {
            $game_over = true;
            $message = "🤝 **引き分け**です。";
        } else {
            // 3. コンピュータ (O) の手番に切り替え
            $current_player = PLAYER_O;
            $message = 'コンピュータが考えています...';

            // コンピュータの手番を実行（ここでは即時実行）
            computer_move($board, $current_player, $message);

            // コンピュータの手番後の再判定
            $winner = check_winner($board);
            if ($winner) {
                $game_over = true;
                $message = "😢 コンピュータ【{$winner}】の**勝利**です...";
            } elseif (is_board_full($board)) {
                $game_over = true;
                $message = "🤝 **引き分け**です。";
            }
        }

    } else {
        $message = 'そのマスは既に埋まっているか、あなたの手番ではありません。';
    }
}


// ----------------------------------------------------
// V. HTML表示
// ----------------------------------------------------
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>PHP 三目並べ (Tic-Tac-Toe)</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            text-align: center;
            padding-top: 30px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        .message {
            padding: 10px;
            margin: 20px 0;
            font-weight: bold;
            border-radius: 5px;
            background-color: #e9ecef;
        }

        /* 修正箇所：盤面全体とセルのスタイルを調整 */
        .board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            width: 300px;
            /* サイズ固定 */
            height: 300px;
            /* サイズ固定 */
            margin: 20px auto;
            /* 💡 盤面全体のボーダーは削除し、セルに持たせる */
            border: none;
        }

        .cell-button {
            width: 100%;
            height: 100%;
            /* 💡 各セルのボーダーを設定 */
            border: 2px solid #333;

            /* 💡 がたつき修正の核心：隣接するセルのボーダーが重なって太くならないように、マージンで相殺 */
            margin-top: -1px;
            margin-left: -1px;

            font-size: 48px;
            font-weight: bold;
            cursor: pointer;
            background: #fff;
            transition: background-color 0.2s;
            outline: none;

            /* テキストを完全に中央に配置 */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            /* ボタンのデフォルトパディングを削除 */
        }

        .cell-button:hover:not([disabled]) {
            background-color: #eee;
        }

        .cell-button[disabled] {
            cursor: default;
        }

        .X-mark {
            color: #d9534f;
        }

        .O-mark {
            color: #5cb85c;
        }

        .reset-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .reset-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>⭕❌ 三目並べ ⭕❌</h1>

        <div class="message">
            <?php echo $message; ?>
        </div>

        <div class="board">
            <form method="POST" action="tictactoe.php">
                <?php for ($r = 0; $r < BOARD_SIZE; $r++): ?>
                    <?php for ($c = 0; $c < BOARD_SIZE; $c++): ?>
                        <?php
                        $value = $board[$r][$c];
                        $disabled = ($value !== CELL_EMPTY || $game_over) ? 'disabled' : '';
                        $class = ($value === PLAYER_X) ? 'X-mark' : (($value === PLAYER_O) ? 'O-mark' : '');
                        ?>
                        <button type="submit" name="move" value="<?php echo "{$r}_{$c}"; ?>"
                            class="cell-button <?php echo $class; ?>" <?php echo $disabled; ?>>
                            <?php echo $value; ?>
                        </button>
                    <?php endfor; ?>
                <?php endfor; ?>
            </form>
        </div>

        <a href="?reset=1" class="reset-button">ゲームをリセット</a>
        <br><br>
        <a href="./index.php">ゲーム一覧に戻る</a>
    </div>
</body>

</html>