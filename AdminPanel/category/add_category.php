
<?php include("../db.php"); ?>

<?php
if(isset($_POST['submit'])){
    $name  = $_POST['name'];
    $desc  = $_POST['description'];
    $status = $_POST['status'];

    // IMAGE VALIDATION conversion to BLOB
    $imageData = null;

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $imageData = addslashes(file_get_contents($_FILES['image']['tmp_name']));
    }

    $query = "INSERT INTO category_details (Category_Name, Category_Image, Description, Status)
              VALUES ('$name', '$imageData', '$desc', '$status')";

    mysqli_query($connection, $query);

    header("Location: ../layout.php?view=categories&msg=added");
    exit;

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Category</title>
<link rel="stylesheet"
 href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">
<h2 class="fw-bold">Add Category</h2>

<form method="POST" enctype="multipart/form-data">
    
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" required class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Category Image</label>
        <input type="file" name="image" accept="image/*" required class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Enabled">Enabled</option>
            <option value="Disabled">Disabled</option>
        </select>
    </div>

    <button type="submit" name="submit" class="btn btn-success">Add</button>
    <a href="../layout.php?view=categories" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
