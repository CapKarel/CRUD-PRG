<?php
session_start();

$file = 'profile.json';
$data = json_decode(file_get_contents($file), true);
$interests = &$data['interests']; // Reference pro snazší manipulaci

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $newItem = trim($_POST['interest'] ?? '');
        
        // Validace: prázdné pole
        if ($newItem === '') {
            $_SESSION['msg'] = ["Pole nesmí být prázdné.", "error"];
        } 
        // Validace: duplicita (case-insensitive)
        elseif (in_array(strtolower($newItem), array_map('strtolower', $interests))) {
            $_SESSION['msg'] = ["Tento zájem už existuje.", "error"];
        } 
        else {
            $interests[] = $newItem;
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
            $_SESSION['msg'] = ["Zájem byl úspěšně přidán.", "success"];
        }
    }
    header("Location: index.php");
    exit;
}

$message = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']); // Smažeme zprávu, aby se při dalším refresh nezobrazila
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>IT Profil 5.0</title>
</head>
<body>

    <?php if ($message): ?>
        <div class="message <?= $message[1] ?>">
            <?= htmlspecialchars($message[0]) ?>
        </div>
    <?php endif; ?>

    <h1>Moje zájmy</h1>

    <ul>
        <?php foreach ($interests as $index => $interest): ?>
            <li>
                <?= htmlspecialchars($interest) ?>
                
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $index ?>">
                    <button type="submit">Smazat</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="interest" placeholder="Nový zájem...">
        <button type="submit">Přidat</button>
    </form>

</body>
</html>
