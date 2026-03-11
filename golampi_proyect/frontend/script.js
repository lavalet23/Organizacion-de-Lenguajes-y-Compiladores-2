const API_URL = "http://localhost/golampi_proyect/backend/execute.php";

const codeEditor = document.getElementById("code-editor");
const output = document.getElementById("output");
const errorsPanel = document.getElementById("errors-panel");
const symbolsPanel = document.getElementById("symbols-panel");

const runBtn = document.getElementById("run-btn");
const clearBtn = document.getElementById("clear-btn");
const exampleBtn = document.getElementById("example-btn");

const downloadOutputBtn = document.getElementById("download-output-btn");
const downloadErrorsBtn = document.getElementById("download-errors-btn");
const downloadSymbolsBtn = document.getElementById("download-symbols-btn");

const statusText = document.getElementById("status-text");
const statusDot = document.getElementById("status-dot");

let lastResult = {
    success: false,
    output: [],
    errors: [],
    symbols: []
};

runBtn.addEventListener("click", runCode);
clearBtn.addEventListener("click", clearAll);
exampleBtn.addEventListener("click", loadExample);

downloadOutputBtn.addEventListener("click", downloadOutput);
downloadErrorsBtn.addEventListener("click", downloadErrors);
downloadSymbolsBtn.addEventListener("click", downloadSymbols);

function setStatus(text, type = "idle") {
    statusText.textContent = text;
    statusDot.className = "status-dot " + type;
}

async function runCode() {
    const code = codeEditor.value;

    output.textContent = "Ejecutando...";
    renderErrors([]);
    renderSymbols([]);
    setStatus("Ejecutando...", "running");

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ code: code })
        });

        const text = await response.text();

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            lastResult = {
                success: false,
                output: [],
                errors: ["El backend no devolvió JSON válido."],
                symbols: []
            };

            output.textContent = "ERROR: El backend no devolvió JSON válido.\n\n" + text;
            renderErrors(lastResult.errors);
            renderSymbols([]);
            setStatus("Respuesta inválida del backend", "error");
            return;
        }

        lastResult = normalizeResponse(data);

        renderOutput(lastResult.output);

        if (!lastResult.success) {
            renderErrors(lastResult.errors);
            renderSymbols(lastResult.symbols);
            setStatus("Ejecución con errores", "error");
            return;
        }

        renderErrors(lastResult.errors);
        renderSymbols(lastResult.symbols);
        setStatus("Ejecución correcta", "success");

    } catch (error) {
        lastResult = {
            success: false,
            output: [],
            errors: ["Error de conexión: " + error.message],
            symbols: []
        };

        output.textContent = "ERROR DE CONEXIÓN:\n" + error.message;
        renderErrors(lastResult.errors);
        renderSymbols([]);
        setStatus("Error de conexión", "error");
    }
}

function normalizeResponse(data) {
    return {
        success: Boolean(data.success),
        output: normalizeOutput(data.output),
        errors: normalizeErrors(data.errors, data.message),
        symbols: normalizeSymbols(data.symbols)
    };
}

function normalizeOutput(rawOutput) {
    if (Array.isArray(rawOutput)) {
        return rawOutput;
    }

    if (typeof rawOutput === "string" && rawOutput.trim() !== "") {
        return [rawOutput];
    }

    return [];
}

function normalizeErrors(rawErrors, fallbackMessage) {
    if (Array.isArray(rawErrors)) {
        return rawErrors;
    }

    if (typeof rawErrors === "string" && rawErrors.trim() !== "") {
        return [rawErrors];
    }

    if (fallbackMessage) {
        return [fallbackMessage];
    }

    return [];
}

function normalizeSymbols(rawSymbols) {
    if (Array.isArray(rawSymbols)) {
        return rawSymbols;
    }

    return [];
}

function renderOutput(outputData) {
    if (Array.isArray(outputData) && outputData.length > 0) {
        output.textContent = outputData.join("\n");
    } else {
        output.textContent = "Programa ejecutado sin salida.";
    }
}

function renderErrors(errors) {
    if (!errors || errors.length === 0) {
        errorsPanel.className = "report";
        errorsPanel.innerHTML = "<div class='ok-message'>No se encontraron errores.</div>";
        return;
    }

    errorsPanel.className = "report";

    let html = "";

    errors.forEach((error, index) => {
        if (typeof error === "string") {
            html += `
                <div class="error-item">
                    <div class="error-title">Error ${index + 1}</div>
                    <div class="error-line">${escapeHtml(error)}</div>
                </div>
            `;
        } else {
            html += `
                <div class="error-item">
                    <div class="error-title">Error ${index + 1}</div>
                    ${error.type ? `<div class="error-line"><strong>Tipo:</strong> ${escapeHtml(error.type)}</div>` : ""}
                    ${error.message ? `<div class="error-line"><strong>Mensaje:</strong> ${escapeHtml(error.message)}</div>` : ""}
                    ${error.line ? `<div class="error-line"><strong>Línea:</strong> ${escapeHtml(String(error.line))}</div>` : ""}
                    ${error.column ? `<div class="error-line"><strong>Columna:</strong> ${escapeHtml(String(error.column))}</div>` : ""}
                </div>
            `;
        }
    });

    errorsPanel.innerHTML = html;
}

function renderSymbols(symbols) {
    if (!symbols || symbols.length === 0) {
        symbolsPanel.className = "report empty";
        symbolsPanel.innerHTML = "No hay símbolos para mostrar.";
        return;
    }

    symbolsPanel.className = "report";

    let html = `
        <div class="symbols-wrapper">
            <table class="symbols-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Identificador</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Ámbito</th>
                        <th>Línea</th>
                    </tr>
                </thead>
                <tbody>
    `;

    symbols.forEach((symbol, index) => {
        if (typeof symbol === "object" && symbol !== null) {
            const identifier = formatCell(symbol.id ?? symbol.name ?? "-");
            const type = formatCell(symbol.type ?? "-");
            const value = formatSymbolValue(symbol.value);
            const scope = formatCell(symbol.scope ?? "global");
            const line = formatCell(symbol.line ?? "-");

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td class="symbol-name">${identifier}</td>
                    <td class="symbol-type">${type}</td>
                    <td class="symbol-value">${value}</td>
                    <td class="symbol-scope">${scope}</td>
                    <td>${line}</td>
                </tr>
            `;
        } else {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td colspan="5" class="symbol-empty">${escapeHtml(String(symbol))}</td>
                </tr>
            `;
        }
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    symbolsPanel.innerHTML = html;
}

function clearAll() {
    codeEditor.value = "";
    output.textContent = "Aquí aparecerá la salida del programa.";
    errorsPanel.className = "report empty";
    errorsPanel.textContent = "No hay errores para mostrar.";
    symbolsPanel.className = "report empty";
    symbolsPanel.textContent = "No hay símbolos para mostrar.";
    lastResult = {
        success: false,
        output: [],
        errors: [],
        symbols: []
    };
    setStatus("Editor limpio", "idle");
}

function loadExample() {
    codeEditor.value = `func main() {
    fmt.Println("Hola desde Golampi")
    fmt.Println(10 + 20)
}`;
    setStatus("Ejemplo cargado", "idle");
}

function downloadOutput() {
    const content = (lastResult.output && lastResult.output.length > 0)
        ? lastResult.output.join("\n")
        : "Programa ejecutado sin salida.";

    downloadFile("salida_golampi.txt", content);
}

function downloadErrors() {
    let content = "REPORTE DE ERRORES - GOLAMPI\n";
    content += "========================================\n\n";

    if (!lastResult.errors || lastResult.errors.length === 0) {
        content += "No se encontraron errores.\n";
    } else {
        lastResult.errors.forEach((error, index) => {
            content += `Error ${index + 1}\n`;

            if (typeof error === "string") {
                content += `Mensaje: ${error}\n`;
            } else {
                if (error.type) content += `Tipo: ${error.type}\n`;
                if (error.message) content += `Mensaje: ${error.message}\n`;
                if (error.line) content += `Línea: ${error.line}\n`;
                if (error.column) content += `Columna: ${error.column}\n`;
            }

            content += "----------------------------------------\n";
        });
    }

    downloadFile("reporte_errores_golampi.txt", content);
}

function downloadSymbols() {
    let content = "TABLA DE SÍMBOLOS - GOLAMPI\n";
    content += "========================================\n\n";

    if (!lastResult.symbols || lastResult.symbols.length === 0) {
        content += "No hay símbolos para mostrar.\n";
    } else {
        lastResult.symbols.forEach((symbol, index) => {
            content += `Símbolo ${index + 1}\n`;

            if (typeof symbol === "object" && symbol !== null) {
                content += `Identificador: ${symbol.id ?? symbol.name ?? "-"}\n`;
                content += `Tipo: ${symbol.type ?? "-"}\n`;
                content += `Valor: ${symbol.value ?? "-"}\n`;
                content += `Ámbito: ${symbol.scope ?? "global"}\n`;
                content += `Línea: ${symbol.line ?? "-"}\n`;
            } else {
                content += `Valor: ${symbol}\n`;
            }

            content += "----------------------------------------\n";
        });
    }

    downloadFile("tabla_simbolos_golampi.txt", content);
}

function downloadFile(filename, content) {
    const blob = new Blob([content], { type: "text/plain;charset=utf-8" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    URL.revokeObjectURL(url);
}

function escapeHtml(text) {
    return String(text)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function formatCell(value) {
    if (value === null || value === undefined || value === "") {
        return "<span class='symbol-empty'>-</span>";
    }
    return escapeHtml(String(value));
}

function formatSymbolValue(value) {
    if (value === null || value === undefined) {
        return "<span class='symbol-empty'>-</span>";
    }

    if (typeof value === "object") {
        try {
            return escapeHtml(JSON.stringify(value));
        } catch (e) {
            return escapeHtml(String(value));
        }
    }

    return escapeHtml(String(value));
}
