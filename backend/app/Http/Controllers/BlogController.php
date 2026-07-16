<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    private array $sources = [
        [
            'nom'     => 'ADEME Agriculture & Alimentation',
            'url'     => 'https://librairie.ademe.fr/rss/3516-thematique-agriculture-alimentation-foret-bioeconomie.xml',
            'couleur' => '#059669',
            'icone'   => '🌾',
        ],
        [
            'nom'     => 'ADEME Économie circulaire',
            'url'     => 'https://librairie.ademe.fr/rss/3426-thematique-economie-circulaire-et-dechets.xml',
            'couleur' => '#1d4ed8',
            'icone'   => '♻️',
        ],
        [
            'nom'     => 'Actu-Environnement Déchets',
            'url'     => 'https://www.actu-environnement.com/flux/rss/dechets/',
            'couleur' => '#dc2626',
            'icone'   => '🗑️',
        ],
        [
            'nom'     => 'Actu-Environnement Agroécologie',
            'url'     => 'https://www.actu-environnement.com/flux/rss/agroecologie/',
            'couleur' => '#7c3aed',
            'icone'   => '🌱',
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
        // Téléchargement parallèle via curl_multi (toutes sources simultanées)
        $multiHandle = curl_multi_init();
        $handles     = [];

        foreach ($this->sources as $key => $source) {
            $ch = curl_init($source['url']);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; AntiGaspiCI/1.0)',
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 3,
            ]);
            curl_multi_add_handle($multiHandle, $ch);
            $handles[$key] = $ch;
        }

        // Exécuter toutes les requêtes en parallèle
        do {
            $status = curl_multi_exec($multiHandle, $active);
            if ($active) curl_multi_select($multiHandle, 1.0);
        } while ($active && $status === CURLM_OK);

        $all = [];

        foreach ($handles as $key => $ch) {
            $xml  = curl_multi_getcontent($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);

            $source = $this->sources[$key];

            if (!$xml || $code !== 200) continue;

            $rss = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$rss || !isset($rss->channel->item)) continue;

            foreach ($rss->channel->item as $item) {
                $description = strip_tags((string) ($item->description ?? ''));
                $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                $description = trim(mb_substr($description, 0, 200));
                if (mb_strlen($description) === 200) $description .= '…';

                $date = strtotime((string) ($item->pubDate ?? '')) ?: 0;

                $image     = null;
                $enclosure = $item->enclosure ?? null;
                if ($enclosure) {
                    $type = (string) ($enclosure['type'] ?? '');
                    if (str_starts_with($type, 'image/')) {
                        $image = (string) ($enclosure['url'] ?? null);
                    }
                }
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

        curl_multi_close($multiHandle);

        usort($all, fn($a, $b) => $b['date'] - $a['date']);

        return $all;
    }
}
