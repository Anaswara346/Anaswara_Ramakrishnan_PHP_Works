function checkAge() {
    const age = Number(document.getElementById("age").value);

    const result = document.getElementById("result");

    if (age >=18) {
        result.textContent = "You are eligible to vote!";
        result.style.color = "green";
    }
    else {
        result.textContent = "You are not eligible to vote!";
        result.style.color = "red";
    }
    if (age == ""){
        result.textContent = "Please enter the Age!";
    }
}