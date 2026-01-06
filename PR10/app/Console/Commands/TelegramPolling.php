<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use App\Services\KitsuService;
use App\Models\TelegramUser;

class TelegramPolling extends Command
{
    protected $signature = 'app:telegram-polling';
    protected $description = 'Telegram bot polling';

    private static array $lastLists = [];
    private static array $lastCommand = [];
    private static array $lastPage = [];

    public function handle()
    {
        $telegram = new Api(config('telegram.bots.default.token'));
        $this->info('Telegram polling started...');

        $offset = 0;
        while (true) {
            try {
                $updates = $telegram->getUpdates(['offset' => $offset, 'timeout' => 30]);
            } catch (\Exception $e) {
                $this->error('Telegram API error: ' . $e->getMessage());
                sleep(5);
                continue;
            }

            foreach ($updates as $update) {
                $offset = $update->getUpdateId() + 1;

                if ($update->getMessage()) {
                    $this->handleMessage($telegram, $update->getMessage());
                } elseif ($update->getCallbackQuery()) {
                    $this->handleCallback($telegram, $update->getCallbackQuery());
                }
            }
        }
    }

    private function handleMessage(Api $telegram, $message)
    {
        $chatId = $message->getChat()->getId();
        $text = trim($message->getText() ?? '');
        $kitsu = app(KitsuService::class);

        TelegramUser::firstOrCreate(
            ['telegram_id' => $chatId],
            ['username' => $message->getFrom()->getUsername() ?? '']
        );

        if ($text === '/start') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "AnimeList Bot\n\n" .
                    "/anime [сторінка] — весь список\n" .
                    "/search <назва> — пошук\n" .
                    "/filter <жанр> [сторінка] — за жанром\n" .
                    "/d <id> — деталі за ID\n" .
                    "/favorites — улюблені\n\n"
            ]);
            return;
        }

        if (str_starts_with($text, '/anime')) {
            $page = (int)trim(substr($text, 6)) ?: 1;
            $page = max(1, $page);
            $list = $kitsu->getPage($page);
            $this->sendListWithButtons($telegram, $chatId, $list, $page, 'anime');
            self::$lastCommand[$chatId] = 'anime';
            self::$lastPage[$chatId] = $page;
            return;
        }

        if (str_starts_with($text, '/search')) {
            $query = trim(substr($text, 8));
            if ($query === '') {
                $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Використання: /search <назва аніме>']);
                return;
            }
            $list = $kitsu->search($query, 1);
            $this->sendListWithButtons($telegram, $chatId, $list, 1, 'search|' . $query);
            self::$lastCommand[$chatId] = 'search|' . $query;
            self::$lastPage[$chatId] = 1;
            return;
        }

        if (str_starts_with($text, '/filter')) {
            $parts = array_filter(explode(' ', trim(substr($text, 7))));
            $genre = $parts[0] ?? '';
            $page = (int)($parts[1] ?? 1);
            if ($genre === '') {
                $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Використання: /filter <жанр> [сторінка]']);
                return;
            }
            $list = $kitsu->filterByGenre($genre, $page);
            $this->sendListWithButtons($telegram, $chatId, $list, $page, 'filter|' . $genre);
            self::$lastCommand[$chatId] = 'filter|' . $genre;
            self::$lastPage[$chatId] = $page;
            return;
        }

        if (str_starts_with($text, '/d ')) {
            $id = (int)trim(substr($text, 3));
            if ($id <= 0) {
                $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Використання: /d <id>']);
                return;
            }
            $this->showDetail($telegram, $chatId, $id);
            return;
        }

        if ($text === '/favorites') {
            $this->showFavorites($telegram, $chatId);
            return;
        }
    }

    private function handleCallback(Api $telegram, $callbackQuery)
    {
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $data = $callbackQuery->getData();
        $kitsu = app(KitsuService::class);

        $telegram->answerCallbackQuery(['callback_query_id' => $callbackQuery->getId()]);

        // Деталі аніме
        if (str_starts_with($data, 'detail_')) {
            $index = (int)substr($data, 7);
            if (!isset(self::$lastLists[$chatId][$index])) return;

            $anime = self::$lastLists[$chatId][$index];
            $this->showDetail($telegram, $chatId, $anime['id'], $index);
            return;
        }

        // Пагінація
        if (str_starts_with($data, 'page_')) {
            [$pagePart, $command] = explode('|', substr($data, 5), 2);
            $page = (int)$pagePart;

            if ($command === 'anime') {
                $list = $kitsu->getPage($page);
            } elseif (str_starts_with($command, 'search|')) {
                $query = substr($command, 7);
                $list = $kitsu->search($query, $page);
            } elseif (str_starts_with($command, 'filter|')) {
                $genre = substr($command, 7);
                $list = $kitsu->filterByGenre($genre, $page);
            } else {
                return;
            }

            $this->sendListWithButtons($telegram, $chatId, $list, $page, $command);
            self::$lastCommand[$chatId] = $command;
            self::$lastPage[$chatId] = $page;
            return;
        }

        // Початок додавання до улюблених
        if (str_starts_with($data, 'add_fav_')) {
            $index = (int)substr($data, 8);
            if (!isset(self::$lastLists[$chatId][$index])) return;

            $keyboard = [
                [['text' => 'Дивлюся', 'callback_data' => 'status_' . $index . '_watching']],
                [['text' => 'Переглянув', 'callback_data' => 'status_' . $index . '_completed']],
                [['text' => 'Закинув', 'callback_data' => 'status_' . $index . '_dropped']],
                [['text' => 'Планую', 'callback_data' => 'status_' . $index . '_planned']],
                [['text' => '⬅ Назад', 'callback_data' => 'detail_' . $index]],
            ];

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Обери статус аніме:',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
            return;
        }

        // Вибір статусу
        if (str_starts_with($data, 'status_')) {
            [$indexStr, $status] = explode('_', substr($data, 7), 2);
            $index = (int)$indexStr;
            if (!isset(self::$lastLists[$chatId][$index])) return;

            self::$lastLists[$chatId]['temp_status'] = $status;

            $keyboard = [];
            $row = [];
            for ($i = 1; $i <= 10; $i++) {
                $row[] = ['text' => (string)$i, 'callback_data' => 'rating_' . $index . '_' . $i];
                if (count($row) === 5) {
                    $keyboard[] = $row;
                    $row = [];
                }
            }
            if ($row) $keyboard[] = $row;
            $keyboard[] = [['text' => '⬅ Назад', 'callback_data' => 'add_fav_' . $index]];

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Обери рейтинг (1-10):',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
            return;
        }

        // Збереження в улюблені
        if (str_starts_with($data, 'rating_')) {
            [$indexStr, $ratingStr] = explode('_', substr($data, 7), 2);
            $index = (int)$indexStr;
            $rating = (int)$ratingStr;

            if (!isset(self::$lastLists[$chatId][$index]) || !isset(self::$lastLists[$chatId]['temp_status'])) return;

            $anime = self::$lastLists[$chatId][$index];
            $status = self::$lastLists[$chatId]['temp_status'];
            unset(self::$lastLists[$chatId]['temp_status']);

            $user = TelegramUser::where('telegram_id', $chatId)->first();
            $favorites = $user->favorites ?? [];

            $found = false;
            foreach ($favorites as &$fav) {
                if ($fav['id'] == $anime['id']) {
                    $fav['rating'] = $rating;
                    $fav['status'] = $status;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $favorites[] = [
                    'title' => $anime['title'] ?? 'Без назви',
                    'id' => $anime['id'],
                    'rating' => $rating,
                    'status' => $status
                ];
            }

            $user->favorites = $favorites;
            $user->save();

            $telegram->answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
                'text' => "Додано в улюблені! Рейтинг: $rating | Статус: $status",
                'show_alert' => true
            ]);

            $this->showDetail($telegram, $chatId, $anime['id'], $index);
            return;
        }

        // Видалення з улюблених
        if (str_starts_with($data, 'remove_fav_')) {
            $index = (int)substr($data, 11);

            $user = TelegramUser::where('telegram_id', $chatId)->first();
            $favorites = $user->favorites ?? [];

            if (isset($favorites[$index])) {
                unset($favorites[$index]);
                $favorites = array_values($favorites);
                $user->favorites = $favorites;
                $user->save();
            }

            $telegram->answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
                'text' => 'Видалено з улюблених!',
                'show_alert' => false
            ]);

            $this->showFavorites($telegram, $chatId);
            return;
        }

        // Назад до списку
        if ($data === 'back') {
            $command = self::$lastCommand[$chatId] ?? 'anime';
            $page = self::$lastPage[$chatId] ?? 1;

            if ($command === 'anime') {
                $list = $kitsu->getPage($page);
            } elseif (str_starts_with($command, 'search|')) {
                $query = substr($command, 7);
                $list = $kitsu->search($query, $page);
            } elseif (str_starts_with($command, 'filter|')) {
                $genre = substr($command, 7);
                $list = $kitsu->filterByGenre($genre, $page);
            } else {
                return;
            }

            $this->sendListWithButtons($telegram, $chatId, $list, $page, $command);
            return;
        }
    }

    private function sendListWithButtons(Api $telegram, int $chatId, array $list, int $page, string $command)
    {
        if (empty($list)) {
            $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Нічого не знайдено на цій сторінці.']);
            return;
        }

        self::$lastLists[$chatId] = $list;

        $text = "Сторінка $page\n\n";
        $keyboard = [];
        $row = [];

        foreach ($list as $i => $anime) {
            $n = $i + 1;
            $text .= "$n. {$anime['title']}\n";
            $row[] = ['text' => (string)$n, 'callback_data' => 'detail_' . $i];
            if (count($row) === 5) {
                $keyboard[] = $row;
                $row = [];
            }
        }
        if ($row) $keyboard[] = $row;

        $nav = [];
        if ($page > 1) {
            $nav[] = ['text' => '⬅ Попередня', 'callback_data' => 'page_' . ($page - 1) . '|' . $command];
        }
        $nav[] = ['text' => '➡ Наступна', 'callback_data' => 'page_' . ($page + 1) . '|' . $command];
        $keyboard[] = $nav;

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }

    private function showDetail(Api $telegram, int $chatId, int $animeId, ?int $index = null)
    {
        $kitsu = app(KitsuService::class);
        $detail = $kitsu->getById($animeId);

        if (empty($detail)) {
            $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Аніме не знайдено.']);
            return;
        }

        $title = $detail['title'] ?? 'Без назви';
        $synopsis = mb_substr($detail['synopsis'] ?? 'Опис відсутній', 0, 800);
        $rating = $detail['rating'] ?? 'N/A';
        $episodes = $detail['episodeCount'] ?? 'N/A';
        $photo = $detail['posterImage']['original'] ?? null;

        $text = "🎬 $title\n\n$synopsis\n\nРейтинг: $rating | Епізоди: $episodes";

        $keyboard = [
            [['text' => '⭐ Додати до улюблених', 'callback_data' => 'add_fav_' . ($index ?? '0')]],
            [['text' => '⬅ Назад до списку', 'callback_data' => 'back']]
        ];

        if ($photo) {
            $telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $photo,
                'caption' => $text,
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
        } else {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
        }
    }

    private function showFavorites(Api $telegram, int $chatId)
    {
        $user = TelegramUser::where('telegram_id', $chatId)->first();
        $favorites = $user->favorites ?? [];

        if (empty($favorites)) {
            $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'У тебе ще немає улюблених аніме.']);
            return;
        }

        $text = "❤️ Твої улюблені:\n\n";
        $keyboard = [];
        $row = [];

        foreach ($favorites as $i => $fav) {
            $text .= ($i + 1) . ". {$fav['title']} — {$fav['rating']} — {$fav['status']}\n";
            $row[] = ['text' => "🗑 " . ($i + 1), 'callback_data' => 'remove_fav_' . $i];
            if (count($row) === 5) {
                $keyboard[] = $row;
                $row = [];
            }
        }
        if ($row) $keyboard[] = $row;

        self::$lastLists[$chatId] = $favorites;

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }
}
