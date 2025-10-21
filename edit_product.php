<?php
require 'db.php'; // ✅ database connection

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<p style='color:red;'>Invalid Product ID.</p>";
    exit;
}

$error = "";

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($name === '' || $price <= 0) {
        $error = "Name and price are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
            $stmt->execute([$name, $price, $id]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error updating product: " . htmlspecialchars($e->getMessage());
        }
    }
}

// ✅ Fetch product info
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<p style='color:red;'>Product not found.</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>
<style>
  body {
    font-family: "Segoe UI", Arial, sans-serif;
    background-color: #f8fafc;
    margin: 0;
    padding: 20px;
    color: #1e293b;
  }

  .container {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  h2 {
    text-align: center;
    color: #0f172a;
    margin-bottom: 20px;
  }

  label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
  }

  input[type="text"],
  input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    margin-top: 5px;
  }

  input:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
  }

  .button {
    display: block;
    width: 100%;
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 12px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    margin-top: 20px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .button:hover {
    background-color: #2563eb;
  }

  .error {
    color: #dc2626;
    background: #fee2e2;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  .back-btn {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #3b82f6;
    font-weight: 600;
  }

  .back-btn:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
<div class="container">
  <h2>Edit Product</h2>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <label>
      Product Name:
      <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
    </label>

    <label>
      Price:
      <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
    </label>

    <button class="button">Update Product</button>
  </form>

  <a href="index.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
