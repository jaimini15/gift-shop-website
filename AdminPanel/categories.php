<?php
// categories.php
<?php
$servername = "localhost";
$username = "root";
$password = "Jaimini@2005";
$dbname = "giftshop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


// Add Category
if(isset($_POST['add_category'])){
    $name = $_POST['category_name'];
    $desc = $_POST['description'];
    $conn->query("INSERT INTO categorie_details (category_name, description, status) VALUES ('$name','$desc','enabled')");
    header("Location: categories.php"); exit;
}

// Enable / Disable
if(isset($_GET['action'])){
    $id = $_GET['id'];
    $action = $_GET['action'];
    if($action=='enable') $conn->query("UPDATE categorie_details SET status='enabled' WHERE category_id=$id");
    if($action=='disable') $conn->query("UPDATE categorie_details SET status='disabled' WHERE category_id=$id");
    header("Location: categories.php"); exit;
}

// Fetch categories
$result = $conn->query("SELECT * FROM categorie_details ORDER BY category_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GiftShop Admin - Categories</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: Arial, sans-serif; }

/* HEADER */
.header { height: 70px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: fixed; top: 0; left: 260px; right: 0; display: flex; align-items: center; padding: 0 30px; justify-content: space-between; z-index: 100; }

/* SIDEBAR */
.sidebar { width: 260px; height: 100vh; background: #343a40; position: fixed; top: 0; left: 0; padding-top: 80px; color: #fff; overflow-y: auto; }
.sidebar a { padding: 12px 20px; display: block; color: #fff; font-size: 16px; text-decoration: none; }
.sidebar a i { margin-right: 10px; }
.sidebar a:hover, .sidebar a.active { background: #495057; }

/* CONTENT AREA */
.content { margin-left: 260px; padding: 20px; margin-top: 90px; }
.card-box { border-radius: 12px; padding: 20px; background: #fff; box-shadow: 0px 2px 6px rgba(0,0,0,0.1); }
.form-control:focus { box-shadow: none; border-color: #0d6efd; }
.btn-primary { background-color: #0d6efd; border-color: #0d6efd; }
.btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="logo fw-bold fs-4">
    <i class="fa-solid fa-gift text-danger"></i> GiftShop Admin
  </div>
  <div>
    <i class="fa-solid fa-user-shield"></i> Admin
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
  <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
  <a href="#" class="active"><i class="fa-solid fa-layer-group"></i> Categories</a>
  <a href="products.php"><i class="fa-solid fa-box"></i> Products</a>
  <a href="stock.php"><i class="fa-solid fa-boxes-packing"></i> Stock</a>
  <a href="orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
  <a href="payments.php"><i class="fa-solid fa-credit-card"></i> Payments</a>
  <a href="delivery.php"><i class="fa-solid fa-truck"></i> Delivery</a>
  <a href="feedback.php"><i class="fa-solid fa-comments"></i> Feedback</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
  <h2 class="fw-bold mb-3">Category Management</h2>

  <!-- Add Category Form -->
  <div class="card-box mb-4">
    <h5 class="mb-3">Add New Category</h5>
    <form method="POST" class="row g-3">
      <div class="col-md-4"><input type="text" name="category_name" class="form-control" placeholder="Category Name" required></div>
      <div class="col-md-6"><input type="text" name="description" class="form-control" placeholder="Description"></div>
      <div class="col-md-2"><button type="submit" name="add_category" class="btn btn-primary w-100">Add</button></div>
    </form>
  </div>

  <!-- Categories Table -->
  <div class="card-box">
    <h5 class="mb-3">All Categories</h5>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
      <?php while($row = $result->fetch_assoc()){ ?>
        <tr>
          <td><?= $row['category_id'] ?></td>
          <td><?= $row['category_name'] ?></td>
          <td><?= $row['description'] ?></td>
          <td><?= ucfirst($row['status']) ?></td>
          <td>
            <?php if($row['status']=='enabled'){ ?>
              <a href="?action=disable&id=<?= $row['category_id'] ?>" class="btn btn-sm btn-warning">Disable</a>
            <?php } else { ?>
              <a href="?action=enable&id=<?= $row['category_id'] ?>" class="btn btn-sm btn-success">Enable</a>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>

</div>

</body>
</html>
