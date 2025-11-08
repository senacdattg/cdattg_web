const http = require('http');
const { URL } = require('url');
const { validarCedula } = require('./sofia-validator');

const PORT = process.env.PORT || 3000;
const MAX_BODY_SIZE = parseInt(process.env.MAX_BODY_SIZE || '1048576', 10); // 1MB por defecto

function sendJson(res, statusCode, data) {
  const payload = JSON.stringify(data);
  res.writeHead(statusCode, {
    'Content-Type': 'application/json; charset=utf-8',
    'Content-Length': Buffer.byteLength(payload),
  });
  res.end(payload);
}

async function handleValidation(cedula) {
  if (!cedula || typeof cedula !== 'string' || !cedula.trim()) {
    return {
      statusCode: 400,
      payload: {
        status: 'error',
        message: 'Debe proporcionar una cédula válida.',
      },
    };
  }

  try {
    const resultado = await validarCedula(cedula.trim());
    return {
      statusCode: 200,
      payload: {
        status: 'ok',
        cedula: cedula.trim(),
        resultado,
      },
    };
  } catch (error) {
    return {
      statusCode: 500,
      payload: {
        status: 'error',
        message: 'Ocurrió un error interno al validar la cédula.',
        detail: error.message,
      },
    };
  }
}

const server = http.createServer(async (req, res) => {
  try {
    if (req.method === 'POST' && req.url === '/validate') {
      let body = '';
      req.on('data', chunk => {
        body += chunk;
        if (body.length > MAX_BODY_SIZE) {
          req.destroy(new Error('Payload demasiado grande.'));
        }
      });

      req.on('end', async () => {
        let cedula;
        try {
          const parsed = JSON.parse(body || '{}');
          cedula = parsed.cedula ?? parsed.documento ?? parsed.identificacion;
        } catch (e) {
          sendJson(res, 400, {
            status: 'error',
            message: 'El cuerpo de la solicitud debe ser JSON válido.',
          });
          return;
        }

        const { statusCode, payload } = await handleValidation(cedula);
        sendJson(res, statusCode, payload);
      });

      req.on('error', err => {
        sendJson(res, 400, {
          status: 'error',
          message: err.message,
        });
      });

      return;
    }

    if (req.method === 'GET') {
      const url = new URL(req.url, `http://${req.headers.host}`);
      if (url.pathname === '/validate') {
        const cedula =
          url.searchParams.get('cedula') ||
          url.searchParams.get('documento') ||
          url.searchParams.get('identificacion');

        const { statusCode, payload } = await handleValidation(cedula);
        sendJson(res, statusCode, payload);
        return;
      }

      if (url.pathname === '/health') {
        sendJson(res, 200, { status: 'ok' });
        return;
      }
    }

    sendJson(res, 404, {
      status: 'error',
      message: 'Ruta no encontrada.',
    });
  } catch (error) {
    sendJson(res, 500, {
      status: 'error',
      message: 'Error inesperado en el servidor.',
      detail: error.message,
    });
  }
});

server.listen(PORT, () => {
  console.error(`🚀 Servidor Playwright escuchando en puerto ${PORT}`);
});


