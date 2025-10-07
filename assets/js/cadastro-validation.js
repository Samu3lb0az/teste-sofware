document.getElementById('cadastroForm').addEventListener('submit', function(event) {
    var senha = document.getElementById('senha').value;
    var confirmaSenha = document.getElementById('confirma_senha').value;
    var errorDiv = document.querySelector('.error-message');

    if (senha.length < 6) {
        alert('A senha deve ter pelo menos 6 caracteres.');
        event.preventDefault(); 
        return;
    }

    if (senha !== confirmaSenha) {
        alert('As senhas não coincidem!');
        event.preventDefault(); 
    }
});