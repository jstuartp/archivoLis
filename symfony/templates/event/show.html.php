<section>
    <a class=\"btn secondary\" href=\"/\">&larr; Regresar</a>
    <h2 style=\"margin-top:1.5rem;\">Evento <?= htmlspecialchars($eventCode) ?></h2>

    <div class=\"summary\">
        <div class=\"card\">
            <h3 style=\"margin-top:0;\">Epicentro</h3>
            <p><?= htmlspecialchars($summary['epicenter'] ?? 'Sin información') ?></p>
        </div>
        <div class=\"card\">
            <h3 style=\"margin-top:0;\">Fecha del evento</h3>
            <p><?= htmlspecialchars($summary['eventDate'] ?? 'Sin registro') ?></p>
        </div>
        <div class=\"card\">
            <h3 style=\"margin-top:0;\">Magnitud</h3>
            <p><?= htmlspecialchars($summary['eventMagnitude'] !== null ? (string) $summary['eventMagnitude'] : 'Sin dato') ?></p>
        </div>
        <div class=\"card\">
            <h3 style=\"margin-top:0;\">Archivos detectados</h3>
            <p><?= count($files) ?></p>
        </div>
    </div>

    <h3>Archivos .Lis</h3>
    <?php if (empty($files)): ?>
        <div class=\"message\">No se encontraron archivos .Lis dentro de la carpeta del evento.</div>
    <?php else: ?>
        <form method=\"post\" action=\"/event/<?= rawurlencode($eventCode) ?>/download\">
            <div class=\"table-actions\">
                <div>
                    <strong>Ordenar por:</strong>
                    <a class=\"sort-link\" href=\"?sort=station\">Código de estación</a>
                    |
                    <a class=\"sort-link\" href=\"?sort=pga\">PGA máximo</a>
                </div>
                <button type=\"submit\" class=\"btn\">Descargar seleccionados</button>
            </div>
            <table>
                <thead>
                <tr>
                    <th><input type=\"checkbox\" id=\"select-all\"></th>
                    <th>Archivo</th>
                    <th>Código de estación</th>
                    <th>PGA máximo (g)</th>
                    <th>Descargar</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td>
                            <label class=\"checkbox\">
                                <input type=\"checkbox\" name=\"files[]\" value=\"<?= htmlspecialchars($file['name']) ?>\">
                            </label>
                        </td>
                        <td><?= htmlspecialchars($file['name']) ?></td>
                        <td><?= htmlspecialchars($file['station_code'] ?? 'Sin dato') ?></td>
                        <td>
                            <?php if ($file['pga_max'] !== null): ?>
                                <?= number_format((float) $file['pga_max'], 3) ?>
                            <?php else: ?>
                                N/D
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class=\"btn secondary\" href=\"/event/<?= rawurlencode($eventCode) ?>/download?file=<?= rawurlencode($file['name']) ?>\">Descargar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</section>
<script>
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            document.querySelectorAll('input[name=\"files[]\"]').forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
        });
    }
</script>
