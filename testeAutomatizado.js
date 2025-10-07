/**
 * testeAutomatizado.js
 *
 * Este script executa testes automatizados para as funcionalidades de Login e Cadastro,
 * navegando pelo site a partir da página inicial (index.php) e gerando um relatório.
 *
 * Como rodar:
 * 1. Certifique-se de que o Node.js está instalado.
 * 2. Instale as dependências com o comando: npm install selenium-webdriver
 * 3. Verifique se o chromedriver.exe está na mesma pasta que este arquivo.
 * 4. Certifique-se de que seu servidor local (XAMPP) está rodando.
 * 5. Execute o script com o comando: node testeAutomatizado.js
 */

const { Builder, By, until } = require('selenium-webdriver');
const fs = require('fs');
const path = require('path');

// ===================================================================================
// --- CONFIGURAÇÕES GERAIS ---
// ===================================================================================

const TARGET_URL = "http://localhost/teste-sofware/index.php";
const SCREENSHOT_DIR = path.join('assets', 'screenshots');
const TIMEOUT_MS = 10000;

fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

// ===================================================================================
// --- CASOS DE TESTE (PREENCHA CONFORME SEU BANCO DE DADOS) ---
// ===================================================================================

const loginTestCases = [
    { email: "usuario.valido@email.com", senha: "senha123", descricao: "Login com credenciais validas" },
    { email: "usuario.valido@email.com", senha: "senha-errada", descricao: "Login com senha incorreta" },
    { email: "nao.existe@email.com", senha: "senha123", descricao: "Login com email nao cadastrado" },
    { email: "", senha: "", descricao: "Login com campos vazios" },
];

const timestamp = Date.now();
const registrationTestCases = [
    { nome: "Usuario Teste Valido", email: `teste.${timestamp}@email.com`, senha: "senha123", confirma_senha: "senha123", descricao: "Cadastro com dados validos e unicos" },
    { nome: "Usuario Existente", email: `teste.existente@email.com`, senha: "senha123", confirma_senha: "senha123", descricao: "Cadastro com email ja existente" },
    { nome: "Usuario Senha Diferente", email: `teste.${timestamp+1}@email.com`, senha: "senha123", confirma_senha: "senha-diferente", descricao: "Cadastro com senhas que nao coincidem" },
    { nome: "", email: "", senha: "", confirma_senha: "", descricao: "Cadastro com campos vazios" },
];

// ===================================================================================
// --- FUNÇÕES DE EXECUÇÃO DOS TESTES ---
// ===================================================================================

async function runLoginTests(report) {
    console.log("\n--- INICIANDO SUÍTE DE TESTES DE LOGIN ---");
    for (const testCase of loginTestCases) {
        const testId = `login_${testCase.descricao.replace(/\s/g, '_')}`;
        console.log(`\nExecutando teste: ${testCase.descricao}`);
        
        const driver = await new Builder().forBrowser('chrome').build();
        
        try {
            await driver.get(TARGET_URL);
            await driver.findElement(By.id('btn-ir-login')).click();
            await driver.wait(until.elementLocated(By.id('email')), TIMEOUT_MS);
            
            if (testCase.email) await driver.findElement(By.id('email')).sendKeys(testCase.email);
            if (testCase.senha) await driver.findElement(By.id('senha')).sendKeys(testCase.senha);
            await driver.findElement(By.id('btn-login')).click();
            
            let resultMessage = "";
            let status = "fail";

            if (testCase.descricao.includes("validas")) {
                 await driver.wait(until.urlContains('homepage.php'), TIMEOUT_MS);
                 status = "pass";
                 resultMessage = "Redirecionado para a homepage com sucesso.";
            } else {
                const mensagemDiv = await driver.wait(until.elementLocated(By.id('mensagem')), TIMEOUT_MS);
                resultMessage = await mensagemDiv.getText();
                status = resultMessage ? "pass" : "fail";
            }
            
            report[testId] = { ...testCase, status, resultMessage, screenshot: await takeScreenshot(driver, testId) };
        } catch (error) {
            console.error("ERRO NO TESTE:", error.message);
            report[testId] = { ...testCase, status: 'fail', resultMessage: error.toString(), screenshot: await takeScreenshot(driver, testId) };
        } finally {
            await driver.quit();
        }
    }
}

async function runRegistrationTests(report) {
    console.log("\n--- INICIANDO SUÍTE DE TESTES DE CADASTRO ---");
    for (const testCase of registrationTestCases) {
        const testId = `cadastro_${testCase.descricao.replace(/\s/g, '_')}`;
        console.log(`\nExecutando teste: ${testCase.descricao}`);
        
        const driver = await new Builder().forBrowser('chrome').build();

        try {
            await driver.get(TARGET_URL);
            await driver.findElement(By.id('btn-ir-cadastro')).click();
            await driver.wait(until.elementLocated(By.id('nome')), TIMEOUT_MS);

            if(testCase.nome) await driver.findElement(By.id('nome')).sendKeys(testCase.nome);
            if(testCase.email) await driver.findElement(By.id('email')).sendKeys(testCase.email);
            if(testCase.senha) await driver.findElement(By.id('senha')).sendKeys(testCase.senha);
            if(testCase.confirma_senha) await driver.findElement(By.id('confirma_senha')).sendKeys(testCase.confirma_senha);
            await driver.findElement(By.id('btn-cadastrar')).click();

            let resultMessage = "";
            let status = "fail";

            if (testCase.descricao.includes("validos")) {
                await driver.wait(until.urlContains('homepage.php'), TIMEOUT_MS);
                status = "pass";
                resultMessage = "Cadastro bem-sucedido e redirecionado para a homepage.";
            } else {
                const mensagemDiv = await driver.wait(until.elementLocated(By.id('mensagem')), TIMEOUT_MS);
                resultMessage = await mensagemDiv.getText();
                status = resultMessage ? "pass" : "fail";
            }

            report[testId] = { ...testCase, status, resultMessage, screenshot: await takeScreenshot(driver, testId) };
        } catch (error) {
            console.error("ERRO NO TESTE:", error.message);
            report[testId] = { ...testCase, status: 'fail', resultMessage: error.toString(), screenshot: await takeScreenshot(driver, testId) };
        } finally {
            await driver.quit();
        }
    }
}

// ===================================================================================
// --- FUNÇÕES AUXILIARES E PRINCIPAL ---
// ===================================================================================

async function takeScreenshot(driver, testId) {
    try {
        const screenshot = await driver.takeScreenshot();
        const screenshotPath = path.join(SCREENSHOT_DIR, `${testId}.png`);
        fs.writeFileSync(screenshotPath, screenshot, 'base64');
        return screenshotPath;
    } catch (e) {
        console.error("Nao foi possivel salvar o screenshot:", e.message);
        return "Falha ao salvar screenshot";
    }
}

async function main() {
    const report = {};

    await runLoginTests(report);
    await runRegistrationTests(report);
    
    fs.writeFileSync('relatorio.json', JSON.stringify(report, null, 2));
    console.log("\n--- TESTES FINALIZADOS ---");
    console.log("Relatório completo salvo em relatorio.json");
    console.log(`Todos os screenshots foram salvos em: ${SCREENSHOT_DIR}`);
}

main();