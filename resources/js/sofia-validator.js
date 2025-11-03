// Script para validar registro en SenaSofiaPlus
import { chromium } from 'playwright';

async function validarCedula(cedula, maxRetries = 3) {
  let browser = null;
  let context = null;
  let page = null;

  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      console.error(`🔄 Intento ${attempt}/${maxRetries} - Validando cédula: ${cedula}`);

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

      console.error(`🔗 Cargando página para cédula: ${cedula}`);
      const response = await page.goto("https://betowa.sena.edu.co/registrarse", {
        waitUntil: "networkidle",
        timeout: 30000
      });

      if (!response.ok()) {
        throw new Error(`HTTP ${response.status()}: ${response.statusText()}`);
      }

      console.error("✅ Página cargada exitosamente");

      // Paso 1. Seleccionar tipo de documento
      console.error("📋 Seleccionando tipo de documento...");
      await page.getByRole("form", { name: "Crear cuenta en Betowa" })
                  .getByRole("button").first().click();
      await page.getByRole("option", { name: "Cédula de Ciudadanía" }).click();

      // Paso 2. Llenar cédula
      console.error(`🧾 Llenando cédula: ${cedula}`);
      await page.getByRole("textbox").fill(cedula);

      // Paso 3. Seleccionar ubicación (hardcodeada)
      console.error("📍 Seleccionando ubicación...");
      await page.getByRole("button", { name: "Seleccionar ubicación" }).click();
      await page.getByRole("textbox", { name: "Buscar ciudad..." }).fill("san jose del gua");
      await page.getByRole("button", { name: "SAN JOSÉ DEL GUAVIARE" }).click();

      // Paso 4. Seleccionar fecha de nacimiento (hardcodeada)
      console.error("📅 Seleccionando fecha de nacimiento...");
      await page.getByRole("button", { name: "placeholder" }).click();
      await page.getByRole("button", { name: "2025" }).click();
      await page.getByRole("button", { name: "2005" }).click();
      await page.getByRole("button", { name: "Noviembre" }).click();
      await page.getByRole("button", { name: "Abril" }).click();
      await page.getByRole("button", { name: "9", exact: true }).click();

      // Paso 5. Aceptar términos
      console.error("✅ Aceptando términos...");
      await page.getByRole("checkbox", { name: /Acepto Términos de uso/ }).check();

      // Paso 6. Enviar formulario
      console.error("📤 Enviando formulario...");
      await page.getByRole("button", { name: "Continuar →" }).click();

      // Paso 7. Esperar respuesta - lógica mejorada
      console.error("⏳ Esperando respuesta del servidor...");

      // Esperar un poco más para que aparezca cualquier modal
      await page.waitForTimeout(12000); // Aumentar a 12 segundos

      // PRIMERO: Buscar modal de error específicamente - múltiples selectores
      const modalSelectors = [
        'div[role="dialog"]',
        '.modal',
        '[class*="modal"]',
        '[id*="modal"]',
        '.swal2-popup', // SweetAlert2
        '.alert',
        '[class*="alert"]',
        '.notification',
        '.toast',
        '.error',
        '[class*="error"]'
      ];

      let modalFound = false;
      let modalText = '';

      for (const selector of modalSelectors) {
        try {
          const elements = await page.locator(selector).all();
          for (const element of elements) {
            const isVisible = await element.isVisible().catch(() => false);
            if (isVisible) {
              const text = await element.innerText().catch(() => '');
              if (text && text.trim().length > 0) {
                modalFound = true;
                modalText = text;
                console.error(`💬 Modal encontrado con selector ${selector}:\n${text}`);
                break;
              }
            }
          }
        } catch (error) {
          // Ignorar errores de selector
          continue;
        }
        if (modalFound) break;
      }

      let result;
      if (modalFound) {
        result = { type: 'modal', text: modalText };
      } else {
        // SEGUNDO: Verificar si hay algún texto de error en la página
        const pageText = await page.innerText().catch(() => '');
        console.error(`📄 Texto completo de la página:\n${pageText}`);

        const errorIndicators = ['ya existe', 'ya cuentas con un registro', 'cuenta registrada', 'documento registrado', 'usuario ya registrado'];

        for (const indicator of errorIndicators) {
          if (pageText.toLowerCase().includes(indicator)) {
            console.error(`⚠️ Texto de error encontrado en página: "${indicator}"`);
            result = { type: 'modal', text: `Error detectado: ${indicator}` };
            modalFound = true;
            break;
          }
        }

        if (!modalFound) {
          // No hay modal ni texto de error, verificar otros indicadores
          const hasEmailField = await page.locator('input[type="email"], input[name*="email"], input[placeholder*="correo"], input[placeholder*="email"]').count().catch(() => 0);
          const hasPasswordField = await page.locator('input[type="password"], input[name*="password"], input[placeholder*="contraseña"]').count().catch(() => 0);
          const hasContinueButton = await page.locator('button[name*="continuar"], button[name*="Continuar"], button[name*="siguiente"]').count().catch(() => 0);

          console.error(`🔍 Estado del formulario - Email: ${hasEmailField}, Password: ${hasPasswordField}, Continue: ${hasContinueButton}`);

          if (hasEmailField > 0 || hasPasswordField > 0) {
            console.error("📧 Campos de registro detectados - puede continuar");
            result = { type: 'form_elements' };
          } else if (hasContinueButton === 0) {
            console.error("✅ Botón 'Continuar' desapareció - proceso avanzó");
            result = { type: 'button_gone' };
          } else {
            console.error("⏰ Timeout alcanzado sin cambios claros");
            result = { type: 'timeout' };
          }
        }
      }

      let resultado;

      if (result.type === 'modal') {
        // Procesar modal de error
        const textoLower = result.text.toLowerCase();
        console.error(`🔍 Procesando modal - Texto completo: "${result.text}"`);

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
                 textoLower.includes("ya cuentas con un registro") ||
                 textoLower.includes("cuenta registrada") ||
                 textoLower.includes("múltiples registros") ||
                 textoLower.includes("se encontraron múltiples")) {
          resultado = "YA_EXISTE";
        }

        // Caso 3: Otro tipo de error
        else {
          console.error(`⚠️ Modal con mensaje no reconocido: ${result.text}`);
          resultado = "DESCONOCIDO";
        }
      } else {
        // No hubo modal de error - puede registrarse
        console.error(`✅ No se detectó modal de error (${result.type}) - usuario puede registrarse`);
        resultado = "NO_REGISTRADO";
      }

      // Log interno, no va a Laravel
      console.error(`✅ RESULTADO FINAL para ${cedula}: ${resultado}`);
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
      console.error(`⏳ Esperando ${waitTime}ms antes del siguiente intento...`);
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
    process.stdout.write("ERROR: No cedula provided\n");
    process.exit(1);
  }

  try {
    const resultado = await validarCedula(cedula);
    // Usar stdout directamente para que Laravel lo capture correctamente
    process.stdout.write(resultado + '\n');
  } catch (error) {
    process.stdout.write("ERROR\n");
    process.exit(1);
  }
}

// Ejecutar si se llama directamente
if (import.meta.url === `file://${process.argv[1]}`) {
  main();
}

export { validarCedula };