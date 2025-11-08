<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - {{ $producto->producto }}</title>
    <style>
        @page { size: auto; margin: 10mm; }
        body { font-family: Arial, sans-serif; }
        .label { width: 80mm; }
        .title { font-size: 12px; margin-bottom: 6px; }
        .barcode { width: 100%; }
        .code { font-size: 11px; text-align: center; margin-top: 4px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body onload="renderAndPrint()">
    <div class="label">
        <div class="title">{{ $producto->producto }}</div>
        <svg id="barcode" class="barcode"></svg>
        <div class="code">{{ $producto->codigo_barras ?? 'SIN CODIGO' }}</div>
    </div>

    <script>
        function renderAndPrint() {
            var value = "{{ $producto->codigo_barras ?? '' }}";
            if (!value) {
                window.print();
                return;
            }
            JsBarcode("#barcode", value, {
                format: "code128",
                width: 2,
                height: 60,
                displayValue: false,
                margin: 0
            });
            setTimeout(function(){ window.print(); }, 250);
        }
    </script>
</body>
</html>



