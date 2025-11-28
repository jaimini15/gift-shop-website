<?php include("../db.php"); ?>

<?php
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $desc = $_POST['description'];

    $query = "INSERT INTO Category_Details (Category_Name, Description, Status) 
              VALUES ('$name', '$desc', 'Enabled')";

    mysqli_query($connection, $query);

    header("Location: categories.php?msg=added");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Category</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">
<h2 class="fw-bold">Add Category</h2>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" required class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <button type="submit" name="submit" class="btn btn-success">Add</button>
</form>
</body>
</html>
