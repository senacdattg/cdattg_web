// Script para validar registro en SenaSofiaPlus
import { chromium } from 'playwright';

async function validarCedula(cedula) {
  // Lanzar el navegador (sin interfaz)
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log(`🔗 Cargando página para cédula: ${cedula}`);
    await page.goto("https://betowa.sena.edu.co/registrarse", { waitUntil: "networkidle" });

    // Paso 1. Seleccionar tipo de documento
    await page.getByRole("form", { name: "Crear cuenta en Betowa" })
                .getByRole("button").first().click();
    await page.getByRole("option", { name: "Cédula de Ciudadanía" }).click();

    // Paso 2. Llenar cédula
    await page.getByRole("textbox").fill(cedula);
    console.log(`🧾 Cédula ingresada: ${cedula}`);

    // Paso 3. Seleccionar ubicación (hardcodeada)
    await page.getByRole("button", { name: "Seleccionar ubicación" }).click();
    await page.getByRole("textbox", { name: "Buscar ciudad..." }).fill("san jose del gua");
    await page.getByRole("button", { name: "SAN JOSÉ DEL GUAVIARE" }).click();

    // Paso 4. Seleccionar fecha de nacimiento (hardcodeada)
    await page.getByRole("button", { name: "placeholder" }).click();
    await page.getByRole("button", { name: "2025" }).click();
    await page.getByRole("button", { name: "2005" }).click();
    await page.getByRole("button", { name: "Octubre" }).click();
    await page.getByRole("button", { name: "Abril" }).click();
    await page.getByRole("button", { name: "9", exact: true }).click();

    // Paso 5. Aceptar términos
    await page.getByRole("checkbox", { name: /Acepto Términos de uso/ }).check();

    // Paso 6. Enviar formulario
    await page.getByRole("button", { name: "Continuar →" }).click();
    console.log("📤 Enviando formulario...");

    // Paso 7. Esperar respuesta
    const dialog = await page.waitForSelector('div[role="dialog"]', { timeout: 15000 });
    const texto = await dialog.innerText();
    console.log(`💬 Respuesta del servidor:\n${texto}`);

    // Determinar resultado
    if (texto.toLowerCase().includes("ya existe") ||
        texto.toLowerCase().includes("ya cuentas con un registro")) {
      return "YA_EXISTE";
    } else if (texto.toLowerCase().includes("creado") ||
               texto.toLowerCase().includes("actualizar tu documento")) {
      return "REQUIERE_CAMBIO";
    } else if (texto.toLowerCase().includes("creado")) {
      return "CUENTA_CREADA";
    } else {
      return "DESCONOCIDO";
    }

  } catch (error) {
    console.log("❌ Error en validación:", error.message);
    return "ERROR";
  } finally {
    await context.close();
    await browser.close();
  }
}

// Función principal que se ejecuta desde línea de comandos
async function main() {
  const cedula = process.argv[2];

  if (!cedula) {
    console.error("❌ Debe proporcionar una cédula como argumento");
    process.exit(1);
  }

  try {
    const resultado = await validarCedula(cedula);
    console.log(resultado); // Solo imprimir el resultado para que lo capture Laravel
  } catch (error) {
    console.error("ERROR");
    process.exit(1);
  }
}

// Ejecutar si se llama directamente
if (import.meta.url === `file://${process.argv[1]}`) {
  main();
}

export { validarCedula };