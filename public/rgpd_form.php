<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RGPD + Firma</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsignature@2.1.2/libs/jSignature.min.js"></script>
    <style>
        body {
            background: #f5f6fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 1rem;
            color: #273c75;
        }

        .rgpd-text {
            margin-bottom: 2rem;
            color: #353b48;
        }

        #signature {
            border: 2px dashed #718093;
            background: #f1f2f6;
            margin: 1rem auto;
            width: 400px;
            height: 200px;
            border-radius: 8px;
        }

        .btn {
            background: #273c75;
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            margin: 0.3rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #40739e;
        }

        .actions {
            margin-bottom: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="rgpdForm" method="post" action="generate_pdf.php">
            <input type="hidden" name="cliente_id" value="12345">
            <h2>Plantilla RGPD</h2>
            <div class="rgpd-text">
                Por la presente, autorizo el tratamiento de mis datos personales conforme al RGPD...
            </div>
            <label><b>Firma:</b></label>
            <div id="signature"></div>
            <input type="hidden" name="firma" id="firmaInput">
            <div class="actions">
                <button type="button" class="btn" onclick="saveSignature()">Guardar Firma</button>
                <button type="button" class="btn" onclick="clearSignature()">Limpiar</button>
                <button type="button" class="btn" onclick="downloadSignature()">Descargar Firma</button>
            </div>
            <button type="submit" class="btn">Generar PDF</button>
        </form>
    </div>
    <script>

        // Inicializamos la firma
        var $sigdiv = $("#signature").jSignature({ 'height': 200, 'width': 400 });

        // Guardar firma
        function saveSignature() {
            var datapair = $sigdiv.jSignature("getData", "image");
            var base64 = datapair[1].split(',')[1] || datapair[1];
            document.getElementById('firmaInput').value = base64;
            alert('Firma guardada. Ahora puedes generar el PDF.');
        }

        // Limpiar firma
        function clearSignature() {
            $sigdiv.jSignature("reset");
            document.getElementById('firmaInput').value = '';
        }

        // Descargar firma como imagen
        function downloadSignature() {
            var datapair = $sigdiv.jSignature("getData", "image");
            var imgData = "data:" + datapair[0] + "," + datapair[1];
            var link = document.createElement('a');
            link.href = imgData;
            link.download = "firma.png";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>

</html>