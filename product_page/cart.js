// -----------------------------
// Cart Panel Open / Close
// -----------------------------
const sidePanel = document.getElementById("sidePanel");
const panelContent = document.getElementById("panelContent");

document.getElementById("cartBtn").onclick = () => {
    sidePanel.classList.add("active");

    fetch("/GitHub/gift-shop-website/product_page/cart_panel.php")
        .then(res => res.text())
        .then(html => {
            panelContent.innerHTML = html;
        })
        .catch(err => {
            console.error("Failed loading panel:", err);
        });
};

document.getElementById("panelClose").onclick = () => {
    sidePanel.classList.remove("active");
};


// -----------------------------
// Event Delegation
// -----------------------------
document.addEventListener("click", function (e) {

    // Remove Item
    if (e.target.classList.contains("remove-btn")) {
        const id = e.target.dataset.id;
        removeItem(id);
        return;
    }

    // Checkout Button
    const btn = e.target.closest(".checkout-btn");
    if (btn) {
        checkoutHandler();
    }
});


// -----------------------------
// Remove Item Logic (Same as your logic)
// -----------------------------
function removeItem(id) {
    if (!confirm("Remove this item from cart?")) return;

    let itemDiv = document.getElementById("item-" + id);
    let hr = itemDiv ? itemDiv.nextElementSibling : null;

    fetch("/GitHub/gift-shop-website/product_page/delete_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
   .then(res => res.json())
.then(data => {

    if (data.status === "success") {

        if (itemDiv) {
            itemDiv.style.transition = "opacity 0.25s";
            itemDiv.style.opacity = "0";
            setTimeout(() => {
                if (itemDiv) itemDiv.remove();
                if (hr && hr.tagName === "HR") hr.remove();
                updateSubtotalFromServer(data.subtotal);
                updateCartCount();
            }, 260);
        }

    } else {
        alert("Delete failed");
        console.error(data);
    }
})

    .catch(err => {
        alert("Network error while deleting. See console.");
        console.error(err);
    });
}


// -----------------------------
// Update Subtotal Logic (Same as your logic)
// -----------------------------
function updateSubtotal() {
    const items = document.querySelectorAll(".item-price");
    let subtotal = 0;
    items.forEach(item => {
        const txt = item.innerText;
        const qty = parseInt(txt.split("×")[0]) || 0;
        const price = parseInt((txt.split("₹")[1] || "0").replace(/,/g,"")) || 0;
        subtotal += qty * price;
    });

    const el = document.getElementById("subtotal-box");
    if (el) el.innerText = "₹" + subtotal.toLocaleString();
}


// -----------------------------
// Update Cart Count Logic (Same as your logic)
// -----------------------------
function updateCartCount() {
    fetch("/GitHub/gift-shop-website/product_page/get_cart_count.php")
        .then(r => r.text())
        .then(text => {
            const num = text.trim();
            const badge = document.querySelector(".cart-badge");
            if (badge) badge.innerText = num;
        })
        .catch(err => console.error("Failed to update cart count:", err));
}


// -----------------------------
// Checkout Logic (Same as your logic)
// -----------------------------
function checkoutHandler() {
    console.log("Checkout clicked ✅");

    fetch("/GitHub/gift-shop-website/view_cart/create_razorpay_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert("Order create failed ❌");
            return;
        }

        const options = {
            key: data.key,
            amount: data.amount,
            currency: "INR",
            order_id: data.orderId,
            name: "My Store",
            description: "Cart Payment",

            handler: function (response) {
                fetch("/GitHub/gift-shop-website/view_cart/confirm_payment.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(response)
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        alert("Payment Successful ✅");
                        location.reload();
                    } else {
                        alert("Payment Failed ❌");
                    }
                });
            }
        };

        new Razorpay(options).open();
    })
    .catch(err => {
        console.error(err);
        alert("Razorpay error ❌");
    });
}
