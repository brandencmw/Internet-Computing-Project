function deleteProduct(event) {
    const buttonID = event.target.id.substring(1);
    const params = buttonID.split(",");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/Internet-Computing-Project/products/deleteProduct.php");
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
    };

    const data = `productID=${encodeURIComponent(params[0])}&supplierID=${encodeURIComponent(params[1])}`;
    xhr.send(data);
}

function purchaseProduct(event) {
    const buttonID = event.target.id.substring(1);
    const params = buttonID.split(",");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/Internet-Computing-Project/products/purchaseProduct.php");
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
    };

    const data = `productID=${encodeURIComponent(params[0])}&supplierID=${encodeURIComponent(params[1])}`;
    xhr.send(data);
}


document.addEventListener("DOMContentLoaded", () => {
    const deleteButtons = Array.from(document.getElementsByClassName("delete"));
    deleteButtons.forEach(button => {
        button.addEventListener("click", (event) => deleteProduct(event));
    });
    
    const purchaseButtons = Array.from(document.getElementsByClassName("purchase"));
    purchaseButtons.forEach(button => {
        button.addEventListener("click", (event) => purchaseProduct(event));
    });
})
