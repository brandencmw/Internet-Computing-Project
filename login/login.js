function onSubmit(event) {
    event.preventDefault();
    const form = document.getElementById("login");

    const username = form.elements["username"].value;
    const password = form.elements["password"].value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/Internet-Computing-Project/login/login.php");
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    alert("Working");
    console.log("WORKING");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.loggedIn) {
                    location.href = "http://localhost/Internet-Computing-Project/"
                } else {
                    alert('Login failed: ' + response.message);
                    const errorBox = document.getElementById("error-box");
                    errorBox.innerHTML = "<h3 style='color: red; text-align: center;'>Incorrect Information. Try again</h3>";
                }
            } else {
                alert('An error occurred. Please try again later.');
            }
        }
    };

    const data = `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
    xhr.send(data);
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("login");
    form.addEventListener("submit", (event) => onSubmit(event));
});