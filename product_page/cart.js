// Cart Panel Open 
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

// Event Delegation
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


// Remove from cart
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
                    if (data.status === "success") {

    if (itemDiv) {
        itemDiv.style.transition = "opacity 0.25s";
        itemDiv.style.opacity = "0";

        setTimeout(() => {
            if (itemDiv) itemDiv.remove();
            if (hr && hr.tagName === "HR") hr.remove();
        }, 260);
    }
}

    updateSubtotal(data.subtotal);
    updateCartCount();
}
, 260);
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
// Update Subtotal
function updateSubtotal(amount) {
    const panel = document.getElementById("panelContent");
    if (!panel) return;

    const subtotalEl = panel.querySelector("#cartSubtotal");
    if (!subtotalEl) return;

    subtotalEl.textContent = "₹" + Number(amount).toLocaleString();
}
 subtotalEl.textContent = "₹" + Number(amount).toLocaleString();

// Update Cart Count 
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


