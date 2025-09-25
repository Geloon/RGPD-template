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

    // Generar el PDF con la firma
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "Por la presente, autorizo el tratamiento de mis datos personales conforme al RGPD...");
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Firma:', 0, 1);
    $pdf->Image($firmaPath, 10, $pdf->GetY(), 60, 30);

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