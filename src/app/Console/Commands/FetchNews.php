<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Facades\Http;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fetchFromNewsApi();
        $this->fetchFromGuardian();
        $this->fetchFromNYT();
        $this->info('News fetching complete.');
    }

    protected function fetchFromNewsApi()
    {
        $apiKey = config('services.newsapi.key');
        $url = 'https://newsapi.org/v2/top-headlines?language=en&pageSize=10&apiKey=' . $apiKey;
        $response = Http::get($url);
        if ($response->ok()) {
            foreach ($response->json('articles', []) as $item) {
                Article::firstOrCreate([
                    'title' => $item['title'],
                    'published_at' => $item['publishedAt'],
                ], [
                    'content' => $item['content'] ?? ($item['description'] ?? ''),
                    'author' => $item['author'] ?? 'Unknown',
                ]);
            }
            $this->info('Fetched from NewsAPI');
        } else {
            $this->error('Failed to fetch from NewsAPI');
            $this->error($response->body());
        }
    }

    protected function fetchFromGuardian()
    {
        $apiKey = config('services.guardian.key');
        $url = 'https://content.guardianapis.com/search?api-key=' . $apiKey . '&show-fields=bodyText,byline&page-size=10';
        $response = Http::get($url);
        if ($response->ok()) {
            foreach ($response->json('response.results', []) as $item) {
                Article::firstOrCreate([
                    'title' => $item['webTitle'],
                    'published_at' => $item['webPublicationDate'],
                ], [
                    'content' => $item['fields']['bodyText'] ?? '',
                    'author' => $item['fields']['byline'] ?? 'Unknown',
                ]);
            }
            $this->info('Fetched from The Guardian');
        } else {
            $this->error('Failed to fetch from The Guardian');
            $this->error($response->body());
        }
    }

    protected function fetchFromNYT()
    {
        $apiKey = config('services.nyt.key');
        $url = 'https://api.nytimes.com/svc/topstories/v2/home.json?api-key=' . $apiKey;
        $response = Http::get($url);
        if ($response->ok()) {
            foreach ($response->json('results', []) as $item) {
                Article::firstOrCreate([
                    'title' => $item['title'],
                    'published_at' => $item['published_date'],
                ], [
                    'content' => $item['abstract'] ?? '',
                    'author' => $item['byline'] ?? 'Unknown',
                ]);
            }
            $this->info('Fetched from NYT');
        } else {
            $this->error('Failed to fetch from NYT');
            $this->error($response->body());
        }
    }
}
