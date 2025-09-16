<?php
namespace App\Controller;

use App\Entity\ArchivoLis;
use App\Framework\FileResponse;
use App\Framework\Response;
use App\Framework\TemplateRenderer;
use App\Repository\ArchivoLisRepository;
use App\Service\EventFileService;

class EventController
{
    public function __construct(
        private TemplateRenderer $renderer,
        private EventFileService $eventFileService,
        private ArchivoLisRepository $archivoLisRepository
    ) {
    }

    public function index(): Response
    {
        $eventCodes = $this->eventFileService->getEventCodes();
        $metadata = $this->archivoLisRepository->findAllIndexedByCode();

        $events = [];
        foreach ($eventCodes as $code) {
            /** @var ArchivoLis|null $dbData */
            $dbData = $metadata[$code] ?? null;
            $summary = $this->eventFileService->getEventSummary($code);

            $events[] = [
                'code' => $code,
                'name' => $dbData?->getEventName() ?? $code,
                'date' => $dbData?->getEventDate()?->format('Y-m-d') ?? ($summary['eventDate'] ?? null),
                'magnitude' => $dbData?->getEventMagnitude() ?? ($summary['eventMagnitude'] ?? null),
                'epicenter' => $summary['epicenter'] ?? null,
            ];
        }

        usort($events, static fn(array $a, array $b) => strcmp($a['code'], $b['code']));

        $html = $this->renderer->render('event/index.html.php', [
            'pageTitle' => 'Eventos disponibles',
            'events' => $events,
        ]);

        return new Response($html);
    }

    public function show(string $eventCode, array $queryParams): Response
    {
        if (!$this->eventFileService->eventExists($eventCode)) {
            return new Response('Evento no encontrado', 404);
        }

        $sort = $queryParams['sort'] ?? null;
        $files = $this->eventFileService->getEventFiles($eventCode);

        if ($sort === 'station') {
            usort($files, static function (array $a, array $b) {
                return strcmp($a['station_code'] ?? '', $b['station_code'] ?? '');
            });
        } elseif ($sort === 'pga') {
            usort($files, static function (array $a, array $b) {
                return ($b['pga_max'] ?? 0) <=> ($a['pga_max'] ?? 0);
            });
        }

        $summary = $this->eventFileService->getEventSummary($eventCode);
        $dbData = $this->archivoLisRepository->findByEventCode($eventCode);

        $html = $this->renderer->render('event/show.html.php', [
            'pageTitle' => sprintf('Evento %s', $eventCode),
            'eventCode' => $eventCode,
            'files' => $files,
            'sort' => $sort,
            'summary' => [
                'epicenter' => $summary['epicenter'] ?? null,
                'eventDate' => $dbData?->getEventDate()?->format('Y-m-d') ?? ($summary['eventDate'] ?? null),
                'eventMagnitude' => $dbData?->getEventMagnitude() ?? ($summary['eventMagnitude'] ?? null),
            ],
        ]);

        return new Response($html);
    }

    public function download(string $eventCode, array $postData, array $queryParams, string $method): FileResponse|Response
    {
        if (!$this->eventFileService->eventExists($eventCode)) {
            return new Response('Evento no encontrado', 404);
        }

        if (strtoupper($method) === 'GET') {
            $file = $queryParams['file'] ?? null;
            if ($file === null) {
                return new Response('No se especificó un archivo para descargar.', 400);
            }

            $files = $this->eventFileService->prepareDownload($eventCode, [$file]);
            if (empty($files)) {
                return new Response('Archivo no encontrado.', 404);
            }

            $file = $files[0];
            return new FileResponse($file['path'], $file['downloadName'], 'text/plain');
        }

        $selected = $postData['files'] ?? [];
        if (!is_array($selected) || empty($selected)) {
            return new Response('Seleccione al menos un archivo para descargar.', 400);
        }

        $files = $this->eventFileService->prepareDownload($eventCode, $selected);
        if (empty($files)) {
            return new Response('No se encontraron archivos válidos para descargar.', 404);
        }

        if (count($files) === 1) {
            $file = $files[0];
            return new FileResponse($file['path'], $file['downloadName'], 'text/plain');
        }

        $zipPath = $this->eventFileService->createZipForFiles($eventCode, $files);

        return new FileResponse($zipPath, sprintf('%s_archivos.zip', $eventCode), 'application/zip', true);
    }
}
