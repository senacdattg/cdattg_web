// Script para validar registro en SenaSofiaPlus
import { chromium } from 'playwright';

async function validarCedula(cedula, maxRetries = 3) {
  let browser = null;
  let context = null;
  let page = null;

  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      console.log(`🔄 Intento ${attempt}/${maxRetries} - Validando cédula: ${cedula}`);

      // Lanzar el navegador con configuración optimizada
      browser = await chromium.launch({
        headless: true,
        args: [
          '--no-sandbox',
          '--disable-setuid-sandbox',
          '--disable-dev-shm-usage',
          '--disable-accelerated-2d-canvas',
          '--no-first-run',
          '--no-zygote',
          '--single-process',
          '--disable-gpu'
        ]
      });

      context = await browser.newContext({
        userAgent: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        viewport: { width: 1280, height: 720 }
      });

      page = await context.newPage();

      // Configurar timeouts más agresivos
      page.setDefaultTimeout(30000);
      page.setDefaultNavigationTimeout(30000);

      console.log(`🔗 Cargando página para cédula: ${cedula}`);
      const response = await page.goto("https://betowa.sena.edu.co/registrarse", {
        waitUntil: "networkidle",
        timeout: 30000
      });

      if (!response.ok()) {
        throw new Error(`HTTP ${response.status()}: ${response.statusText()}`);
      }

      console.log("✅ Página cargada exitosamente");

      // Paso 1. Seleccionar tipo de documento
      console.log("📋 Seleccionando tipo de documento...");
      await page.getByRole("form", { name: "Crear cuenta en Betowa" })
                  .getByRole("button").first().click();
      await page.getByRole("option", { name: "Cédula de Ciudadanía" }).click();

      // Paso 2. Llenar cédula
      console.log(`🧾 Llenando cédula: ${cedula}`);
      await page.getByRole("textbox").fill(cedula);

      // Paso 3. Seleccionar ubicación (hardcodeada)
      console.log("📍 Seleccionando ubicación...");
      await page.getByRole("button", { name: "Seleccionar ubicación" }).click();
      await page.getByRole("textbox", { name: "Buscar ciudad..." }).fill("san jose del gua");
      await page.getByRole("button", { name: "SAN JOSÉ DEL GUAVIARE" }).click();

      // Paso 4. Seleccionar fecha de nacimiento (hardcodeada)
      console.log("📅 Seleccionando fecha de nacimiento...");
      await page.getByRole("button", { name: "placeholder" }).click();
      await page.getByRole("button", { name: "2025" }).click();
      await page.getByRole("button", { name: "2005" }).click();
      await page.getByRole("button", { name: "Octubre" }).click();
      await page.getByRole("button", { name: "Abril" }).click();
      await page.getByRole("button", { name: "9", exact: true }).click();

      // Paso 5. Aceptar términos
      console.log("✅ Aceptando términos...");
      await page.getByRole("checkbox", { name: /Acepto Términos de uso/ }).check();

      // Paso 6. Enviar formulario
      console.log("📤 Enviando formulario...");
      await page.getByRole("button", { name: "Continuar →" }).click();

      // Paso 7. Esperar respuesta - lógica mejorada
      console.log("⏳ Esperando respuesta del servidor...");

      // Usar Promise.race para esperar tanto el modal como un timeout
      const result = await Promise.race([
        // Opción 1: Esperar por modal de error
        (async () => {
          try {
            const dialog = await page.waitForSelector('div[role="dialog"]', {
              timeout: 15000,
              state: 'visible'
            });
            const texto = await dialog.innerText();
            console.log(`💬 Modal de error encontrado:\n${texto}`);
            return { type: 'modal', text: texto };
          } catch (error) {
            // Modal no apareció dentro del timeout
            return { type: 'no_modal' };
          }
        })(),

        // Opción 2: Esperar por indicadores de que puede continuar
        (async () => {
          await page.waitForTimeout(3000); // Esperar 3 segundos mínimo

          // Verificar si cambió la URL (navegación exitosa)
          const currentUrl = page.url();
          if (currentUrl.includes('registro') || currentUrl.includes('siguiente') || currentUrl.includes('continuar')) {
            console.log("🔄 Navegación detectada - puede continuar");
            return { type: 'navigation' };
          }

          // Verificar si aparecieron nuevos elementos del formulario
          const nextFormElements = await page.locator('input[type="email"], input[name*="email"], button[name*="siguiente"]').count();
          if (nextFormElements > 0) {
            console.log("📝 Nuevos elementos de formulario detectados");
            return { type: 'form_elements' };
          }

          // Verificar si desapareció el botón de "Continuar"
          const continueButton = await page.locator('button[name*="continuar"], button[name*="Continuar"]').count();
          if (continueButton === 0) {
            console.log("✅ Botón 'Continuar' desapareció - proceso avanzó");
            return { type: 'button_gone' };
          }

          return { type: 'timeout' };
        })()
      ]);

      let resultado;

      if (result.type === 'modal') {
        // Procesar modal de error
        const textoLower = result.text.toLowerCase();

        // Caso 1: Usuario ya existe y requiere cambio de documento (mensaje específico)
        if ((textoLower.includes("ya existe") || textoLower.includes("ya cuentas con un registro")) &&
            (textoLower.includes("actualizar tu documento") ||
             textoLower.includes("requiere_cambio") ||
             textoLower.includes("cambiar tu documento") ||
             textoLower.includes("tarjeta de identidad"))) {
          resultado = "REQUIERE_CAMBIO";
        }

        // Caso 2: Usuario ya existe y está registrado correctamente
        else if (textoLower.includes("ya existe") ||
                 textoLower.includes("ya cuentas con un registro")) {
          resultado = "YA_EXISTE";
        }

        // Caso 3: Otro tipo de error
        else {
          console.log(`⚠️ Modal con mensaje no reconocido: ${result.text}`);
          resultado = "DESCONOCIDO";
        }
      } else {
        // No hubo modal de error - puede registrarse
        console.log(`✅ No se detectó modal de error (${result.type}) - usuario puede registrarse`);
        resultado = "NO_REGISTRADO";
      }

      console.log(`✅ Validación exitosa para ${cedula}: ${resultado}`);
      return resultado;

    } catch (error) {
      console.error(`❌ Error en intento ${attempt}/${maxRetries} para ${cedula}:`, error.message);

      // Si es el último intento, devolver error
      if (attempt === maxRetries) {
        console.error(`💥 Todos los intentos fallaron para ${cedula}`);
        return "ERROR";
      }

      // Esperar antes del siguiente intento (backoff exponencial)
      const waitTime = Math.min(1000 * Math.pow(2, attempt - 1), 5000);
      console.log(`⏳ Esperando ${waitTime}ms antes del siguiente intento...`);
      await new Promise(resolve => setTimeout(resolve, waitTime));

    } finally {
      // Limpiar recursos
      if (page) {
        try {
          await page.close();
        } catch (e) {
          console.warn("⚠️ Error cerrando página:", e.message);
        }
      }
      if (context) {
        try {
          await context.close();
        } catch (e) {
          console.warn("⚠️ Error cerrando contexto:", e.message);
        }
      }
      if (browser) {
        try {
          await browser.close();
        } catch (e) {
          console.warn("⚠️ Error cerrando navegador:", e.message);
        }
      }
    }
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