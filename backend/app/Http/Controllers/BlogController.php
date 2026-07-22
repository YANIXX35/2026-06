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

        $sources = $this->sources;

        return view('blog.index', compact('articles', 'sources'));
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
                // 1. Contenu HTML brut
                $rawContent = (string) ($item->description ?? '');
                $contentEncoded = (string) $item->children('content', true)->encoded;
                if (!empty($contentEncoded)) {
                    $rawContent .= ' ' . $contentEncoded;
                }

                // Décodage des entités HTML en premier pour révéler les balises <img> et nettoyer le texte
                $rawHtml = html_entity_decode($rawContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                // 2. Extraction d'Image
                $image = null;

                // a) Balise <enclosure>
                if (isset($item->enclosure)) {
                    $url = (string) ($item->enclosure['url'] ?? '');
                    if (!empty($url)) {
                        $image = $url;
                    }
                }

                // b) Namespace media:content ou media:thumbnail
                if (!$image) {
                    $media = $item->children('media', true);
                    if (isset($media->content)) {
                        $image = (string) ($media->content['url'] ?? $media->content->attributes()->url ?? '');
                    }
                    if (!$image && isset($media->thumbnail)) {
                        $image = (string) ($media->thumbnail['url'] ?? $media->thumbnail->attributes()->url ?? '');
                    }
                }

                // c) Namespace itunes:image
                if (!$image) {
                    $itunes = $item->children('itunes', true);
                    if (isset($itunes->image)) {
                        $image = (string) ($itunes->image->attributes()->href ?? '');
                    }
                }

                // d) Extraction par Regex depuis la balise <img src="..."> dans le corps de l'article
                if (!$image && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $rawHtml, $matches)) {
                    $image = $matches[1];
                }

                // Normalisation des URLs relatives d'images
                if ($image) {
                    if (str_starts_with($image, '//')) {
                        $image = 'https:' . $image;
                    } elseif (str_starts_with($image, '/')) {
                        $parsedUrl = parse_url($source['url']);
                        $host = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
                        $image = $host . $image;
                    }
                }

                // e) Image thématique par défaut si aucune n'est présente dans le flux RSS
                if (!$image) {
                    $fallbacks = [
                        'ADEME Agriculture & Alimentation' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=800&q=80',
                        'ADEME Économie circulaire'        => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=800&q=80',
                        'Actu-Environnement Déchets'       => 'https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?auto=format&fit=crop&w=800&q=80',
                        'Actu-Environnement Agroécologie'  => 'https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&w=800&q=80',
                    ];
                    $image = $fallbacks[$source['nom']] ?? 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80';
                }

                // 3. Nettoyage de la description texte (Sans balises HTML)
                $cleanText   = strip_tags($rawHtml);
                $cleanText   = preg_replace('/\s+/', ' ', $cleanText);
                $cleanText   = trim($cleanText);
                $description = mb_substr($cleanText, 0, 180);
                if (mb_strlen($cleanText) > 180) {
                    $description .= '…';
                }

                $title = html_entity_decode(strip_tags((string) $item->title), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $title = preg_replace('/\s+/', ' ', trim($title));
                $date  = strtotime((string) ($item->pubDate ?? '')) ?: 0;

                $all[] = [
                    'titre'       => $title,
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
