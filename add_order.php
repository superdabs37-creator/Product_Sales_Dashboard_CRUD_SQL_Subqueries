<?php
require 'db.php'; // ✅ Database connection

try {
    // ✅ Fetch products for dropdown
    $products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

$error = "";

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $order_date = $_POST['order_date'] ?? date('Y-m-d');

    if ($product_id <= 0 || $quantity <= 0) {
        $error = "Please select a product and enter a quantity greater than 0.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (product_id, quantity, order_date) VALUES (?, ?, ?)");
            $stmt->execute([$product_id, $quantity, $order_date]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error saving order: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Order</title>
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
    margin-bottom: 15px;
    font-weight: 600;
  }

  select,
  input[type="number"],
  input[type="date"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    margin-top: 5px;
  }

  input:focus,
  select:focus {
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
  <h2>Add New Order</h2>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <label>
      Product:
      <select name="product_id" required>
        <option value="">-- Choose a product --</option>
        <?php foreach ($products as $p): ?>
          <option value="<?= htmlspecialchars($p['id']) ?>">
            <?= htmlspecialchars($p['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Quantity:
      <input name="quantity" type="number" min="1" value="1" required>
    </label>

    <label>
      Order Date:
      <input name="order_date" type="date" value="<?= date('Y-m-d') ?>" required>
    </label>

    <button class="button">Save Order</button>
  </form>

  <a href="index.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
