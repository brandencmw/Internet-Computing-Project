
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


document.addEventListener("DOMContentLoaded", () => {
    const supplierForm = document.getElementById("supplier-search");
    const productForm = document.getElementById("product-search");
    const supplierButton = document.getElementById("all-suppliers");
    const productButton = document.getElementById("all-products");
    
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
})