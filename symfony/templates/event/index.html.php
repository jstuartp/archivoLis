<section>
    <h2>Eventos disponibles</h2>
    <p>Seleccione un evento para visualizar los archivos .Lis asociados y consultar sus detalles.</p>
    <?php if (empty($events)): ?>
        <div class=\"message\">No se encontraron eventos en la carpeta configurada.</div>
    <?php else: ?>
        <div class=\"card\">
            <table>
                <thead>
                <tr>
                    <th>Nombre clave</th>
                    <th>Fecha</th>
                    <th>Magnitud</th>
                    <th>Epicentro</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['name'] ?? $event['code']) ?></td>
                        <td><?= htmlspecialchars($event['date'] ?? 'Sin registro') ?></td>
                        <td><?= htmlspecialchars($event['magnitude'] !== null ? number_format((float) $event['magnitude'], 2) : 'N/D') ?></td>
                        <td><?= htmlspecialchars($event['epicenter'] ?? 'N/D') ?></td>
                        <td>
                            <a class=\"btn\" href=\"/event/<?= rawurlencode($event['code']) ?>\">Ver archivos</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
