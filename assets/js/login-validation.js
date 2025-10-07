document.getElementById('loginForm').addEventListener('submit', function(event) {
    var email = document.getElementById('email').value;

    if (email === "") {
        alert('Por favor, preencha o campo de email.');
        event.preventDefault();
    }
});