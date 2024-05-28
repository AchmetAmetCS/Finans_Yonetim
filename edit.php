<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];

        $stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sdsii", $type, $amount, $description, $id, $user_id);

        if ($stmt->execute()) {
            echo "Kayıt güncellendi!";
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Hata: " . $stmt->error;
        }
    } else {
        $stmt = $conn->prepare("SELECT type, amount, description FROM transactions WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->bind_result($type, $amount, $description);
        $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaydı Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('bg13.png') no-repeat center center fixed; 
            background-size: cover;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Kaydı Düzenle</h1>

        <form method="POST" action="edit.php?id=<?php echo $id; ?>">
            <div class="form-group">
                <label for="type">Tür:</label>
                <select id="type" name="type" class="form-control">
                    <option value="income" <?php if ($type == 'income') echo 'selected'; ?>>Gelir</option>
                    <option value="expense" <?php if ($type == 'expense') echo 'selected'; ?>>Gider</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Miktar:</label>
                <input type="number" id="amount" step="0.01" name="amount" class="form-control" value="<?php echo $amount; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama:</label>
                <input type="text" id="description" name="description" class="form-control" value="<?php echo $description; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Güncelle</button>
        </form>
    </div>
</body>
</html>
