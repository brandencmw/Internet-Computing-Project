
function deleteSupplier(event) {
    supplierID = event.target.id;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/Internet-Computing-Project/suppliers/deleteSupplier.php");
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

    const data = `supplierID=${encodeURIComponent(supplierID)}`;
    xhr.send(data);
}

function backToMain() {
    location.href = "http://localhost/Internet-Computing-Project/"
}


document.addEventListener("DOMContentLoaded", () => {
    const buttons = Array.from(document.querySelectorAll("button"));
    const backButton = buttons.shift();

    buttons.forEach(button => {
        button.addEventListener("click", (event) => deleteSupplier(event));
    });

    backButton.addEventListener("click", () => backToMain());
});