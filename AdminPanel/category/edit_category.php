<?php include("../db.php"); ?>

<?php
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM Category_Details WHERE Category_Id=$id"));

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $desc = $_POST['description'];

    mysqli_query($connection, "UPDATE Category_Details 
        SET Category_Name='$name', Description='$desc' 
        WHERE Category_Id=$id");

    header("Location: categories.php?msg=updated");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Category</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">
<h2 class="fw-bold">Edit Category</h2>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" value="<?= $data['Category_Name'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= $data['Description'] ?></textarea>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update</button>
</form>
</body>
</html>
