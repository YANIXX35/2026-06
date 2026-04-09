<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    private array $sources = [
        [
            'nom'     => 'OMS',
            'url'     => 'https://www.who.int/rss-feeds/news-french.xml',
            'couleur' => '#1d4ed8',
            'icone'   => '🌍',
        ],
        [
            'nom'     => 'Le Monde Santé',
            'url'     => 'https://www.lemonde.fr/sante/rss_full.xml',
            'couleur' => '#dc2626',
            'icone'   => '📰',
        ],
        [
            'nom'     => 'Futura Santé',
            'url'     => 'https://www.futura-sciences.com/rss/sante/actualites.xml',
            'couleur' => '#7c3aed',
            'icone'   => '🔬',
        ],
        [
            'nom'     => 'Santé Magazine',
            'url'     => 'https://www.santemagazine.fr/rss',
            'couleur' => '#059669',
            'icone'   => '💊',
        ],
    ];

    public function index()
    {
        $articles = Cache::remember('blog_rss', 3600, function () {
            return $this->fetchAllFeeds();
        });

        $categories = collect($this->sources)->pluck('nom');

        return view('blog.index', compact('articles', 'categories'));
    }

    public function refresh()
    {
        Cache::forget('blog_rss');
        return redirect()->route('blog')->with('success', 'Flux actualisés.');
    }

    private function fetchAllFeeds(): array
    {
        $all = [];
        $ctx = stream_context_create([
            'http' => [
                'timeout'    => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; AntiGaspiCI/1.0)',
            ],
        ]);

        foreach ($this->sources as $source) {
            $xml = @file_get_contents($source['url'], false, $ctx);
            if (!$xml) continue;

            $rss = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$rss || !isset($rss->channel->item)) continue;

            foreach ($rss->channel->item as $item) {
                $description = strip_tags((string) ($item->description ?? ''));
                $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                $description = trim(mb_substr($description, 0, 200));
                if (mb_strlen($description) === 200) $description .= '…';

                $date = strtotime((string) ($item->pubDate ?? '')) ?: 0;

                // Récupère l'image si disponible
                $image = null;
                $enclosure = $item->enclosure ?? null;
                if ($enclosure) {
                    $type = (string) ($enclosure['type'] ?? '');
                    if (str_starts_with($type, 'image/')) {
                        $image = (string) ($enclosure['url'] ?? null);
                    }
                }
                // Cherche aussi dans media:content ou media:thumbnail
                $media = $item->children('media', true);
                if (!$image && isset($media->thumbnail)) {
                    $image = (string) ($media->thumbnail['url'] ?? '');
                }
                if (!$image && isset($media->content)) {
                    $image = (string) ($media->content['url'] ?? '');
                }

                $all[] = [
                    'titre'       => html_entity_decode(strip_tags((string) $item->title), ENT_QUOTES, 'UTF-8'),
                    'lien'        => (string) ($item->link ?? '#'),
                    'description' => $description,
                    'date'        => $date,
                    'date_fmt'    => $date ? date('d/m/Y', $date) : '—',
                    'image'       => $image,
                    'source'      => $source['nom'],
                    'couleur'     => $source['couleur'],
                    'icone'       => $source['icone'],
                ];
            }
        }

        // Tri par date décroissante
        usort($all, fn($a, $b) => $b['date'] - $a['date']);

        return $all;
    }
}
