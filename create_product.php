<?php
require 'db.php';

$error = "";

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($name === '' || $price <= 0) {
        $error = "⚠️ Please enter a valid product name and price greater than 0.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
            $stmt->execute([$name, $price]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f9fb;
      color: #2c3e50;
      margin: 0;
      padding: 0;
    }

    header {
      background: #2980b9;
      color: white;
      padding: 15px 20px;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      letter-spacing: 0.5px;
    }

    .container {
      max-width: 500px;
      background: white;
      margin: 60px auto;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2980b9;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #34495e;
      font-weight: bold;
    }

    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      transition: 0.2s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus {
      border-color: #2980b9;
      box-shadow: 0 0 4px rgba(41, 128, 185, 0.3);
      outline: none;
    }

    .button {
      background: #2980b9;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
      transition: 0.2s;
    }

    .button:hover {
      background: #3498db;
      transform: translateY(-1px);
    }

    .error {
      background: #ffe6e6;
      color: #c0392b;
      border-left: 5px solid #c0392b;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      text-decoration: none;
      color: #2980b9;
      font-weight: bold;
    }

    .back-link:hover {
      color: #1f6fa1;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <header>Product Management System</header>

  <div class="container">
    <h2>Add New Product</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="name">Product Name:</label>
      <input id="name" name="name" type="text" placeholder="Enter product name" required>

      <br><br>

      <label for="price">Price (₱):</label>
      <input id="price" name="price" type="number" step="0.01" placeholder="Enter price" required>

      <br><br>

      <button class="button">💾 Save Product</button>
    </form>

    <a href="index.php" class="back-link">⬅ Back to Dashboard</a>
  </div>

</body>
</html>
