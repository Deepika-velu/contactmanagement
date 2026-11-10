function validateForm() {

    let name = document.querySelector(
        'input[name="name"]'
    ).value.trim();

    let email = document.querySelector(
        'input[name="email"]'
    ).value.trim();

    let phone = document.querySelector(
        'input[name="phone"]'
    ).value.trim();

    if (name === "" || email === "" || phone === "") {
        alert("Please fill all required fields!");
        return false;
    }

    if (!/^[0-9]{10,15}$/.test(phone)) {
        alert("Enter a valid phone number (10-15 digits)!");
        return false;
    }

    return true;
}