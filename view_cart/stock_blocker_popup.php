<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Items Out of Stock</title>
<style>
body {
    margin: 0;
    font-family: Poppins, sans-serif;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

.popup {
    background: #fff;
    width: 420px;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    animation: pop 0.3s ease;
}

@keyframes pop {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.popup h2 {
    color: #d32f2f;
    margin-bottom: 10px;
}

.popup ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.popup li {
    font-weight: 600;
    padding: 6px 0;
}
.popup button {
    margin-top: 15px;
    padding: 10px 25px;
    background: #7e2626d5;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.popup button:hover {
    background: black;
}
</style>
</head>

<body>

<div class="popup">
    <h2> Items Out of Stock</h2>
    <p>Please remove the following product(s) to continue:</p>

    <ul id="productList"></ul>

    <button onclick="goToCart()">home</button>
</div>

<script>
const products = <?= $outOfStockList ?>;
const ul = document.getElementById("productList");

products.forEach(p => {
    const li = document.createElement("li");
    li.textContent = p;
    ul.appendChild(li);
});

function goToCart() {
    window.location.href = "../home page/index.php";
}
</script>

</body>
</html>
