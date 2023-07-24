
function redirectSuppliers(event, supplierName="") {
    event.preventDefault();
    const destURL = "http://localhost/Internet-Computing-Project/suppliers?name=" + supplierName;
    location.href = destURL;
}

function redirectProducts(event, productName="") {
    event.preventDefault();
    const destURL = "http://localhost/Internet-Computing-Project/products?name=" + productName;
    location.href = destURL;
}

function logout(event) {
    event.preventDefault();
    console.log("LOGOUT");
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/Internet-Computing-Project/logout.php");
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            console.log(xhr.responseText);
            const response = JSON.parse(xhr.responseText);
            if (xhr.status === 200) {
                if (response.finished) {
                    location.reload(true);
                }
            } else {
                alert('An error occurred. Please try again later.');
            }
        }
    }
    xhr.send();
}

document.addEventListener("DOMContentLoaded", () => {
    const supplierForm = document.getElementById("supplier-search");
    const productForm = document.getElementById("product-search");
    const supplierButton = document.getElementById("all-suppliers");
    const productButton = document.getElementById("all-products");
    const logoutButton = document.getElementById("log-out");

    console.log("WORKING");
    
    supplierForm.addEventListener("submit", (event) => {
        const supplierName = supplierForm.elements["supplier-name"].value;
        redirectSuppliers(event, supplierName);
    });
    productForm.addEventListener("submit", (event) => {
        const productName = productForm.elements["product-name"].value;
        redirectProducts(event, productName);
    });

    supplierButton.addEventListener("click", (event) => redirectSuppliers(event));
    productButton.addEventListener("click", (event) => redirectProducts(event));
    logoutButton.addEventListener("click", (event) => logout(event));
});