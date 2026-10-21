/* LOGIN */

function login(event) {

    event.preventDefault();

    let email = document.getElementById("loginEmail").value;
    let password = document.getElementById("loginPassword").value;

    if (email === "" || password === "") {
        alert("Please fill all fields.");
        return;
    }

    alert("Login successful!");

    window.location.href = "dashboard.html";
}


/* REGISTER */

function register(event) {

    event.preventDefault();

    alert("Registration successful!");

    window.location.href = "login.html";
}


/* FORGOT PASSWORD */

function forgotPassword(event) {

    event.preventDefault();

    let email = document.getElementById("forgotEmail").value;

    if (email === "") {
        alert("Please enter your email.");
        return;
    }

    alert("Password reset link has been sent!");
}


/* ADD TO CART */

function addToCart(product) {

    alert(product + " added to cart!");
}


/* ADD TO WISHLIST */

function addToWishlist(product) {

    alert(product + " added to wishlist!");
}


/* PRODUCT IMAGE THUMBNAILS */

function changeImage(image) {

    document.getElementById("mainImage").src = image;

}


/* COUPON */

function applyCoupon() {

    let coupon =
        document.getElementById("coupon").value;

    let message =
        document.getElementById("couponMessage");

    let today = new Date();

    let expiryDate =
        new Date("2026-12-31");


    if (coupon === "") {

        message.innerHTML =
            "Please enter a coupon code.";

        return;
    }


    if (today > expiryDate) {

        message.innerHTML =
            "Coupon has expired.";

        return;
    }


    if (coupon === "SAVE10") {

        message.innerHTML =
            "Coupon applied! 10% discount.";

    } else {

        message.innerHTML =
            "Invalid coupon code.";

    }

}


/* PLACE ORDER */

function placeOrder() {

    alert("Order placed successfully!");

    window.location.href = "orders.html";

}


/* REORDER */

function reorder() {

    let stockAvailable = true;

    if (stockAvailable) {

        alert(
            "All products are available. Added to cart!"
        );

        window.location.href = "cart.html";

    } else {

        alert(
            "Some products are currently out of stock."
        );

    }

}


/* SEARCH PRODUCTS */

function searchProducts() {

    let input =
        document.getElementById("search")
        .value.toLowerCase();

    let products =
        document.querySelectorAll(".product-card");


    products.forEach(function(product) {

        let name =
            product.querySelector("h3")
            .innerText
            .toLowerCase();

        if (name.includes(input)) {

            product.style.display = "block";

        } else {

            product.style.display = "none";

        }

    });

}