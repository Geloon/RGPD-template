<?php
require_once __DIR__ . '/fpdf.php';

// Procesar la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firma = $_POST['firma'] ?? '';
    $cliente_id = $_POST['cliente_id'] ?? 'desconocido';

    if (!$firma) {
        die('No se recibió la firma.');
    }

    $firmaData = base64_decode($firma);

    // Guardar la firma como imagen temporal
    $firmaPath = sys_get_temp_dir() . '/firma_' . uniqid() . '.png';
    file_put_contents($firmaPath, $firmaData);

    // Crear carpeta para guardar PDFs e imágenes si no existe
    $dirFirmas = __DIR__ . '/firmas';
    if (!is_dir($dirFirmas)) {
        mkdir($dirFirmas, 0777, true);
    }

    // Nombre del archivo PDF e imagen con el ID del cliente y la fecha/hora
    $timestamp = date('Ymd_His');
    $pdfFile = $dirFirmas . '/rgpd_firmado_' . $cliente_id . '_' . $timestamp . '.pdf';
    $imgFile = $dirFirmas . '/firma_' . $cliente_id . '_' . $timestamp . '.png';

    // Copiar la firma al directorio definitivo
    copy($firmaPath, $imgFile);

    // Generar el PDF con la firma y estilo formal
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetMargins(25, 20, 25);

    // Encabezado con logo
    $logoPath = __DIR__ . '/logo.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 25, 5, 30);
        $pdf->SetY(18);
    } else {
        $pdf->SetY(20);
    }

    // Título centrado debajo del logo
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 12, utf8_decode('Consentimiento RGPD'), 0, 1, 'C');
    $pdf->Ln(2);

    // Línea decorativa debajo del título (ajusta la posición Y)
    $yBarra = $pdf->GetY() + 2;
    $pdf->SetDrawColor(39, 60, 117);
    $pdf->SetLineWidth(1);
    $pdf->Line(25, $yBarra, 185, $yBarra);
    $pdf->Ln(10);

    // Fecha y datos del cliente
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');
    $pdf->Cell(0, 8, utf8_decode('Cliente ID: ') . $cliente_id, 0, 1, 'R');
    $pdf->Ln(5);

    // Texto RGPD
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 8, utf8_decode("Por la presente, autorizo el tratamiento de mis datos personales conforme al RGPD. 
\n\nEste documento certifica que el cliente ha sido informado y otorga su consentimiento para el tratamiento de sus datos personales según la normativa vigente."), 0, 'J');
    $pdf->Ln(20);

    // Recuadro de firma
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, 'Firma:', 0, 1, 'L');
    $yFirma = $pdf->GetY();
    $pdf->Rect(25, $yFirma, 60, 30); // Recuadro para la firma
    $pdf->Image($firmaPath, 27, $yFirma + 2, 56, 26); // Firma dentro del recuadro

    $pdf->SetY($yFirma + 35);
    $pdf->Cell(60, 8, '_________________________', 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(60, 8, utf8_decode('Firma del cliente'), 0, 0, 'L');

    // Guardar el PDF en el servidor
    $pdf->Output('F', $pdfFile);

    // Eliminar la imagen temporal
    unlink($firmaPath);

    // Mostrar mensaje de éxito y enlaces con estilo
    echo '
    <div style="background:#e6ffe6;border:1px solid #4CAF50;padding:20px;border-radius:8px;max-width:400px;margin:30px auto;text-align:center;font-family:sans-serif;">
        <h2 style="color:#388e3c;margin-top:0;">PDF generado y guardado correctamente.</h2>
        <a href="firmas/' . basename($pdfFile) . '" target="_blank" style="display:inline-block;margin:10px 0;padding:10px 20px;background:#4CAF50;color:#fff;text-decoration:none;border-radius:4px;">Descargar PDF firmado</a><br>
        <a href="firmas/' . basename($imgFile) . '" target="_blank" style="display:inline-block;margin:10px 0;padding:10px 20px;background:#2196F3;color:#fff;text-decoration:none;border-radius:4px;">Ver firma como imagen</a>
    </div>
    ';

    exit;
}
?>