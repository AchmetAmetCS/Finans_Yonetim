<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $type, $amount, $description);

    if ($stmt->execute()) {
        echo "Kayıt başarılı!";
    } else {
        echo "Hata: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('bg553.png') no-repeat center center fixed; 
            background-size: cover;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }
        .container {
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:nth-child(odd) {
            background-color: #fff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Dashboard</h1>

        <form method="POST" action="dashboard.php">
            <div class="form-group">
                <label for="type">Tür:</label>
                <select id="type" name="type" class="form-control">
                    <option value="income">Gelir</option>
                    <option value="expense">Gider</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Miktar:</label>
                <input type="number" id="amount" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama:</label>
                <input type="text" id="description" name="description" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </form>

        <?php
        $user_id = $_SESSION['user_id'];
        $result = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id ORDER BY date DESC");

        echo "<h2 class='text-center mt-4'>Kayıtlarınız</h2>";
        echo "<table class='table table-bordered'>";
        echo "<thead class='thead-light'><tr><th>Tür</th><th>Miktar</th><th>Açıklama</th><th>Tarih</th><th>İşlem</th></tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['type'] . "</td>";
            echo "<td>" . $row['amount'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Düzenle</a> 
                    <a href='delete.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Bu kaydı silmek istediğinize emin misiniz?\")'>Sil</a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        ?>
    </div>
</body>
</html>
