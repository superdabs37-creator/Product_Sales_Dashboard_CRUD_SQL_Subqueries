<?php
// Include database connection
require 'db.php';

// Fetch products with total quantity sold using a subquery
$sql = "
  SELECT 
    p.id, 
    p.name, 
    p.price,
    IFNULL(
      (SELECT SUM(o.quantity) FROM orders o WHERE o.product_id = p.id),
      0
    ) AS total_sold
  FROM products p
  ORDER BY p.id ASC;
";

try {
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database query failed: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Sales Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f9fb;
      color: #2c3e50;
      margin: 20px;
    }
    h2 {
      color: #34495e;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      gap: 10px;
    }
    .button-group {
      display: flex;
      gap: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    th {
      background: #2980b9;
      color: white;
    }
    .button {
      background: #3498db;
      color: white;
      border: none;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .button:hover {
      background: #2d89c3;
    }
    .button.analytics {
      background: #27ae60;
    }
    .button.analytics:hover {
      background: #1f9d54;
    }
    .button.add-order {
      background: #f39c12;
    }
    .button.add-order:hover {
      background: #e6890d;
    }
    .inline {
      display: inline;
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <h2>Products</h2>
    <div class="button-group">
      <!-- ✅ New Add Order Button -->
      <a href="add_order.php" class="button add-order">➕ Add Order</a>

      <!-- ✅ Existing Analytics Button -->
      <a href="analytics.php" class="button analytics">📊 View Analytics</a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Total Sold</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['id']) ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td>₱<?= number_format($p['price'], 2) ?></td>
        <td><?= htmlspecialchars($p['total_sold']) ?></td>
        <td>
          <a class="button" href="edit_product.php?id=<?= urlencode($p['id']) ?>">Edit</a>
          <form class="inline" method="post" action="delete_product.php" onsubmit="return confirm('Delete this product?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
            <button class="button" style="background:#c0392b;">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
