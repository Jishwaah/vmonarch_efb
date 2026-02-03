<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SimbriefService
{
    public function fetchLatestForUser(User $user): array
    {
        if (! $user->simbrief_id) {
            return [
                'status' => 'missing_id',
                'message' => 'Add your SimBrief ID to see the latest OFP.',
            ];
        }

        $baseUrl = config('services.simbrief.base_url');
        $jsonMode = config('services.simbrief.json_mode', 'v2');

        $response = Http::timeout(10)
            ->acceptJson()
            ->get($baseUrl, [
                'userid' => $user->simbrief_id,
                'json' => $jsonMode,
            ]);

        if (! $response->ok()) {
            return [
                'status' => 'error',
                'message' => 'Unable to fetch SimBrief data right now.',
            ];
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return [
                'status' => 'error',
                'message' => 'SimBrief returned an unexpected response.',
            ];
        }

        $fetchStatus = data_get($payload, 'fetch.status');
        if ($fetchStatus && strtolower((string) $fetchStatus) !== 'success') {
            return [
                'status' => 'error',
                'message' => 'SimBrief response: '.$fetchStatus,
            ];
        }

        $ofp = $payload['ofp'] ?? $payload;

        $data = $this->parseOfpData($ofp);
        $data['ofp_pdf_url'] = $this->extractPdfUrl($ofp) ?? $this->extractPdfUrl($payload);

        return [
            'status' => 'ok',
            'data' => $data,
            'raw' => $ofp,
        ];
    }

    private function parseOfpData(array $ofp): array
    {
        $data = [
            'origin_iata' => $this->firstNonEmpty([
                data_get($ofp, 'origin.iata_code'),
                data_get($ofp, 'origin.iata'),
            ]),
            'origin_icao' => $this->firstNonEmpty([
                data_get($ofp, 'origin.icao_code'),
                data_get($ofp, 'origin.icao'),
            ]),
            'destination_iata' => $this->firstNonEmpty([
                data_get($ofp, 'destination.iata_code'),
                data_get($ofp, 'destination.iata'),
            ]),
            'destination_icao' => $this->firstNonEmpty([
                data_get($ofp, 'destination.icao_code'),
                data_get($ofp, 'destination.icao'),
            ]),
            'alternate_icao' => $this->firstNonEmpty([
                data_get($ofp, 'alternate.icao_code'),
                data_get($ofp, 'alternate.icao'),
            ]),
            'alternate2_icao' => $this->firstNonEmpty([
                data_get($ofp, 'alternate2.icao_code'),
                data_get($ofp, 'alternate2.icao'),
            ]),
            'cruise_altitude' => $this->firstNonEmpty([
                data_get($ofp, 'general.cruise_altitude'),
                data_get($ofp, 'general.cruise_altitude_ft'),
                data_get($ofp, 'atc.flight_level'),
                data_get($ofp, 'atc.initial_altitude'),
                data_get($ofp, 'plan_altitude'),
            ]),
            'ete' => $this->firstNonEmpty([
                data_get($ofp, 'times.enroute_time'),
                data_get($ofp, 'times.est_time_enroute'),
                data_get($ofp, 'times.ete'),
            ]),
            'etd' => $this->firstNonEmpty([
                data_get($ofp, 'times.out'),
                data_get($ofp, 'times.sched_out'),
                data_get($ofp, 'times.etd'),
            ]),
            'eta' => $this->firstNonEmpty([
                data_get($ofp, 'times.in'),
                data_get($ofp, 'times.sched_in'),
                data_get($ofp, 'times.eta'),
            ]),
        ];

        $text = $this->extractOfpText($ofp);
        if ($text && $this->needsTextFallback($data)) {
            $textData = $this->parseFromText($text);
            $data = array_merge($data, array_filter($textData));
        }

        $data['alternates'] = array_values(array_filter([
            $data['alternate_icao'],
            $data['alternate2_icao'],
        ]));

        return $data;
    }

    private function needsTextFallback(array $data): bool
    {
        return empty($data['origin_icao']) || empty($data['destination_icao']);
    }

    private function extractOfpText(array $ofp): ?string
    {
        $candidates = [
            data_get($ofp, 'text'),
            data_get($ofp, 'ofp_text'),
            data_get($ofp, 'text_plan'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return $candidate;
            }
        }

        return null;
    }

    private function parseFromText(string $text): array
    {
        $result = [
            'origin_icao' => null,
            'destination_icao' => null,
            'alternate_icao' => null,
            'alternate2_icao' => null,
            'cruise_altitude' => null,
            'ete' => null,
            'etd' => null,
            'eta' => null,
        ];

        $lines = preg_split('/\r\n|\r|\n/', $text);
        foreach ($lines as $line) {
            if (! $result['origin_icao'] && preg_match('/FROM\/TO\s*:\s*([A-Z]{4})\s*\/\s*([A-Z]{4})/i', $line, $match)) {
                $result['origin_icao'] = strtoupper($match[1]);
                $result['destination_icao'] = strtoupper($match[2]);
            }

            if (! $result['origin_icao'] && preg_match('/\bORIGIN\s*[:\-]\s*([A-Z]{4})\b/i', $line, $match)) {
                $result['origin_icao'] = strtoupper($match[1]);
            }

            if (! $result['destination_icao'] && preg_match('/\bDEST(?:INATION)?\s*[:\-]\s*([A-Z]{4})\b/i', $line, $match)) {
                $result['destination_icao'] = strtoupper($match[1]);
            }

            if (! $result['alternate_icao'] && preg_match('/\bALTN\s*1?\s*[:\-]\s*([A-Z]{4})\b/i', $line, $match)) {
                $result['alternate_icao'] = strtoupper($match[1]);
            }

            if (! $result['alternate2_icao'] && preg_match('/\bALTN\s*2\s*[:\-]\s*([A-Z]{4})\b/i', $line, $match)) {
                $result['alternate2_icao'] = strtoupper($match[1]);
            }

            if (! $result['cruise_altitude'] && preg_match('/\bCRZ(?:\s*ALT)?\s*[:\-]\s*([A-Z0-9]+)\b/i', $line, $match)) {
                $result['cruise_altitude'] = strtoupper($match[1]);
            }

            if (! $result['ete'] && preg_match('/\bETE\s*[:\-]\s*([0-9]{1,2}:[0-9]{2})/i', $line, $match)) {
                $result['ete'] = $match[1];
            }

            if (! $result['etd'] && preg_match('/\bETD\s*[:\-]\s*([0-9]{1,2}:[0-9]{2})/i', $line, $match)) {
                $result['etd'] = $match[1];
            }

            if (! $result['eta'] && preg_match('/\bETA\s*[:\-]\s*([0-9]{1,2}:[0-9]{2})/i', $line, $match)) {
                $result['eta'] = $match[1];
            }
        }

        return $result;
    }

    private function extractPdfUrl(array $payload): ?string
    {
        $directory = data_get($payload, 'files.directory');
        $pdfLink = data_get($payload, 'files.pdf.link');
        if (is_string($directory) && is_string($pdfLink) && $directory !== '' && $pdfLink !== '') {
            return rtrim($directory, '/').'/'.ltrim($pdfLink, '/');
        }

        $candidates = [
            data_get($payload, 'links.pdf'),
            data_get($payload, 'links.ofp_pdf'),
            data_get($payload, 'links.pdf_url'),
            data_get($payload, 'files.pdf.link'),
            data_get($payload, 'files.pdf'),
            data_get($payload, 'ofp.pdf'),
            data_get($payload, 'ofp.link'),
            data_get($payload, 'link'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && Str::startsWith($candidate, 'http')) {
                return $candidate;
            }
        }

        return null;
    }

    private function firstNonEmpty(array $values): ?string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }
}
