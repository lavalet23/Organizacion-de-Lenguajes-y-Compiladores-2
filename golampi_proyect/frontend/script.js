let lastResult = null;
let lastErrors = null;
let lastSymbols = null;

async function runCode() {
    const code = document.getElementById("code-editor").value;
    const output = document.getElementById("output");

    if (!code.trim()) {
        output.textContent = "ERROR: El editor está vacío.";
        return;
    }

    output.textContent = "Ejecutando...";

    try {
        const response = await fetch("../backend/execute.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ code })
        });

        const data = await response.json();

        if (data.success) {
            lastResult = data.output || [];
            lastSymbols = data.symbols || [];
            lastErrors = data.errors || [];

            output.textContent = lastResult.length
                ? lastResult.join("\n")
                : "Ejecución completada sin salida.";
        } else {
            lastResult = null;
            lastSymbols = null;
            lastErrors = [data.message || "Error desconocido"];

            output.textContent = "ERROR: " + (data.message || "Error desconocido");
        }
    } catch (error) {
        output.textContent = "ERROR DE CONEXIÓN: " + error.message;
        lastResult = null;
        lastSymbols = null;
        lastErrors = [error.message];
    }
}

function clearConsole() {
    document.getElementById("output").textContent = "";
}

function downloadResult() {
    if (!lastResult || lastResult.length === 0) {
        alert("No hay resultado para mostrar.");
        return;
    }

    alert(lastResult.join("\n"));
}

function downloadErrors() {
    if (!lastErrors || lastErrors.length === 0) {
        alert("No hay errores para mostrar.");
        return;
    }

    alert(lastErrors.join("\n"));
}

function downloadSymbolTable() {
    if (!lastSymbols || lastSymbols.length === 0) {
        alert("No hay tabla de símbolos para mostrar.");
        return;
    }

    let text = "TABLA DE SÍMBOLOS\n\n";
    lastSymbols.forEach((symbol, index) => {
        text += `${index + 1}. ID: ${symbol.id}\n`;
        text += `   Tipo: ${symbol.type}\n`;
        text += `   Scope: ${symbol.scope}\n`;
        text += `   Valor: ${formatSymbolValue(symbol.value)}\n\n`;
    });

    alert(text);
}

function formatSymbolValue(value) {
    if (value === null) return "<nil>";
    if (typeof value === "boolean") return value ? "true" : "false";
    if (typeof value === "object") return JSON.stringify(value, null, 2);
    return String(value);
}

document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".editor-header .btn");
    const editor = document.getElementById("code-editor");

    if (buttons[0]) {
        buttons[0].addEventListener("click", () => {
            editor.value = "";
            clearConsole();
        });
    }

    if (buttons[1]) {
        buttons[1].addEventListener("click", () => {
            const input = document.createElement("input");
            input.type = "file";
            input.accept = ".txt,.golampi,.go";

            input.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    editor.value = e.target.result;
                };
                reader.readAsText(file);
            });

            input.click();
        });
    }

    if (buttons[2]) {
        buttons[2].addEventListener("click", () => {
            const blob = new Blob([editor.value], { type: "text/plain;charset=utf-8" });
            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.href = url;
            a.download = "codigo.golampi";
            a.click();

            URL.revokeObjectURL(url);
        });
    }
});