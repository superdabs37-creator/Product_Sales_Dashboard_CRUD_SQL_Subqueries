<?php
// analytics.php
require 'db.php'; // ✅ correct database file
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Analytics</title>
<style>
  body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f8fafc;
    margin: 0;
    padding: 20px;
    color: #1e293b;
  }

  h2 {
    text-align: center;
    color: #0f172a;
    font-size: 2rem;
    margin-bottom: 10px;
  }

  h3 {
    color: #334155;
    border-left: 5px solid #3b82f6;
    padding-left: 10px;
    margin-top: 40px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  }

  th, td {
    text-align: left;
    padding: 12px 15px;
  }

  th {
    background: #3b82f6;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
  }

  tr:nth-child(even) {
    background: #f1f5f9;
  }

  tr:hover {
    background: #e2e8f0;
  }

  td {
    font-size: 0.95rem;
  }

  .container {
    max-width: 1000px;
    margin: 0 auto;
  }

  p {
    margin: 10px 0;
  }

  .back-btn {
    display: inline-block;
    background: #3b82f6;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.2s ease;
  }

  .back-btn:hover {
    background: #2563eb;
  }

</style>
</head>
<body>
<div class="container">
  <a href="index.php" class="back-btn">← Back to Dashboard</a>
  <h2>Analytics (SQL Subqueries)</h2>

<?php
/*
1) Top-selling products by total quantity
*/
$sql_top = "
  SELECT
    p.id,
    p.name,
    p.price,
    IFNULL((SELECT SUM(o.quantity) FROM orders o WHERE o.product_id = p.id), 0) AS total_sold
  FROM products p
  ORDER BY total_sold DESC, p.name ASC
  LIMIT 10;
";
$top = $pdo->query($sql_top)->fetchAll();

echo "<h3>Top-selling Products</h3>";
echo "<table><tr><th>Rank</th><th>Product</th><th>Price</th><th>Total Sold</th></tr>";
$rank = 1;
foreach ($top as $t) {
    echo "<tr><td>{$rank}</td><td>".htmlspecialchars($t['name'])."</td><td>".number_format($t['price'],2)."</td><td>{$t['total_sold']}</td></tr>";
    $rank++;
}
echo "</table>";

/*
2) Products with sales above average
*/
$sql_above_avg = "
  SELECT p.id, p.name,
    IFNULL((SELECT SUM(o.quantity) FROM orders o WHERE o.product_id = p.id), 0) AS total_sold
  FROM products p
  WHERE IFNULL((SELECT SUM(o.quantity) FROM orders o WHERE o.product_id = p.id), 0)
        > (
          SELECT AVG(t.total_sold)
          FROM (
            SELECT IFNULL(SUM(o2.quantity),0) AS total_sold
            FROM products p2
            LEFT JOIN orders o2 ON o2.product_id = p2.id
            GROUP BY p2.id
          ) t
        )
  ORDER BY total_sold DESC;
";
$above = $pdo->query($sql_above_avg)->fetchAll();

echo "<h3>Products with Sales Above Average</h3>";
if (count($above) === 0) {
    echo "<p>No product is above average sales.</p>";
} else {
    echo "<table><tr><th>Product</th><th>Total Sold</th></tr>";
    foreach ($above as $a) {
        echo "<tr><td>".htmlspecialchars($a['name'])."</td><td>{$a['total_sold']}</td></tr>";
    }
    echo "</table>";
}

/*
3) Market share (% of total quantity sold)
*/
$sql_market = "
  SELECT p.name,
    IFNULL(t.total_sold, 0) AS total_sold,
    ROUND(
      100 * IFNULL(t.total_sold, 0) /
      NULLIF((SELECT SUM(qty) FROM (SELECT IFNULL(SUM(o3.quantity),0) AS qty FROM products p3 LEFT JOIN orders o3 ON o3.product_id = p3.id GROUP BY p3.id) tmp), 0),
    2) AS pct_share
  FROM products p
  LEFT JOIN (
    SELECT o.product_id, SUM(o.quantity) AS total_sold
    FROM orders o
    GROUP BY o.product_id
  ) t ON t.product_id = p.id
  ORDER BY total_sold DESC;
";
$market = $pdo->query($sql_market)->fetchAll();

echo "<h3>Market Share by Quantity Sold (%)</h3>";
echo "<table><tr><th>Product</th><th>Total Sold</th><th>Share (%)</th></tr>";
foreach ($market as $m) {
    $pct = $m['pct_share'] === null ? '0.00' : number_format($m['pct_share'], 2);
    echo "<tr><td>".htmlspecialchars($m['name'])."</td><td>{$m['total_sold']}</td><td>{$pct}</td></tr>";
}
echo "</table>";

/*
4) Month-over-month comparison
*/
$current_month = date('Y-m');
$curr_start = $current_month . "-01";
$curr_end = date('Y-m-t', strtotime($curr_start));
$prev_start = date('Y-m-01', strtotime('-1 month', strtotime($curr_start)));
$prev_end = date('Y-m-t', strtotime($prev_start));

$sql_mom = "
  SELECT p.id, p.name,
    (SELECT IFNULL(SUM(o1.quantity),0) FROM orders o1 WHERE o1.product_id = p.id AND o1.order_date BETWEEN ? AND ?) AS sold_curr,
    (SELECT IFNULL(SUM(o2.quantity),0) FROM orders o2 WHERE o2.product_id = p.id AND o2.order_date BETWEEN ? AND ?) AS sold_prev
  FROM products p
  ORDER BY sold_curr DESC;
";
$stmt = $pdo->prepare($sql_mom);
$stmt->execute([$curr_start, $curr_end, $prev_start, $prev_end]);
$mom = $stmt->fetchAll();

echo "<h3>Month-over-Month ({$current_month} vs " . date('Y-m', strtotime($prev_start)) . ")</h3>";
echo "<table><tr><th>Product</th><th>Sold This Month</th><th>Sold Last Month</th><th>Difference</th></tr>";
foreach ($mom as $r) {
    $diff = intval($r['sold_curr']) - intval($r['sold_prev']);
    $color = $diff >= 0 ? "style='color:green;'" : "style='color:red;'";
    echo "<tr><td>".htmlspecialchars($r['name'])."</td><td>{$r['sold_curr']}</td><td>{$r['sold_prev']}</td><td {$color}>{$diff}</td></tr>";
}
echo "</table>";
?>
</div>
</body>
</html>
