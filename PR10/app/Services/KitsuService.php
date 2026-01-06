<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KitsuService
{
    private string $baseUrl = 'https://kitsu.io/api/edge';

    private function client()
    {
        return Http::withHeaders([
            'Accept' => 'application/vnd.api+json',
            'User-Agent' => 'PR10-Anime-Bot/1.0',
        ]);
    }

    // 🔹 Список аніме з пагінацією
    public function getPage(int $page = 1, int $limit = 5): array
    {
        $offset = ($page - 1) * $limit;

        $response = $this->client()->get("{$this->baseUrl}/anime", [
            'page[limit]' => $limit,
            'page[offset]' => $offset,
        ]);

        $data = $response->json('data');
        if (!is_array($data)) return [];

        return $this->mapAnimeList($data);
    }

    // 🔹 Пошук аніме
    public function search(string $query, int $limit = 5): array
    {
        $response = $this->client()->get("{$this->baseUrl}/anime", [
            'filter[text]' => $query,
            'page[limit]' => $limit,
        ]);

        $data = $response->json('data');
        if (!is_array($data)) return [];

        return $this->mapAnimeList($data);
    }

    // 🔹 Деталі по ID
    public function getById(int $id): ?array
    {
        $response = $this->client()->get("{$this->baseUrl}/anime/{$id}");
        if (!$response->successful()) return null;

        $item = $response->json('data');
        if (!is_array($item) || !isset($item['attributes'])) return null;

        return [
            'id' => (int) ($item['id'] ?? 0),
            'title' => $item['attributes']['canonicalTitle'] ?? 'Без назви',
            'synopsis' => $item['attributes']['synopsis'] ?? '',
        ];
    }

    // 🔹 Єдиний mapper
    private function mapAnimeList(array $data): array
    {
        return array_map(function ($item) {
            return [
                'id' => (int) ($item['id'] ?? 0),
                'title' => $item['attributes']['canonicalTitle'] ?? 'Без назви',
                'synopsis' => $item['attributes']['synopsis'] ?? '',
            ];
        }, $data);
    }
}
