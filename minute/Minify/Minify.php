<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 9/13/2016
 * Time: 4:34 PM
 */

namespace Minute\Minify {

    use App\Config\BootLoader;
    use App\Model\MMinifiedDatum;
    use Assetic\Asset\AssetCollection;
    use Assetic\Asset\HttpAsset;
    use Assetic\Asset\StringAsset;
    use Assetic\Filter\CssImportFilter;
    use Assetic\Filter\CssMinFilter;
    use Assetic\Filter\CssRewriteFilter;
    use Carbon\Carbon;
    use DOMDocument;
    use Masterminds\Html5;
    use Minute\Cache\QCache;
    use Minute\Config\Config;
    use Minute\Debug\Debugger;
    use Minute\Event\ResponseEvent;
    use Minute\Filter\JSConcatFilter;
    use Minute\Http\HttpResponseEx;
    use Minute\Log\LoggerEx;
    use Minute\Utils\PathUtils;

    class Minify {
        const MINIFY_KEY = 'minify';
        /**
         * @var string
         */
        protected $version;
        /**
         * @var
         */
        protected $jsMinifier;
        /**
         * @var QCache
         */
        private $cache;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var BootLoader
         */
        private $bootLoader;
        /**
         * @var PathUtils
         */
        private $utils;
        /**
         * @var LoggerEx
         */
        private $logger;
        /**
         * @var Debugger
         */
        private $debugger;

        /**
         * Minify constructor.
         *
         * @param QCache $cache
         * @param Config $config
         * @param BootLoader $bootLoader
         * @param PathUtils $utils
         * @param LoggerEx $logger
         * @param Debugger $debugger
         */
        public function __construct(QCache $cache, Config $config, BootLoader $bootLoader, PathUtils $utils, LoggerEx $logger, Debugger $debugger) {
            $this->cache      = $cache;
            $this->config     = $config;
            $this->bootLoader = $bootLoader;
            $this->utils      = $utils;
            $this->logger     = $logger;
            $this->debugger   = $debugger;

            MMinifiedDatum::unguard();
        }

        public function minify(ResponseEvent $event) {
            if ($event->isSimpleHtmlResponse()) {
                $settings = $this->cache->get('minify-settings', function () {
                    return $this->config->get(self::MINIFY_KEY);
                }, 3600);

                if (($settings['css']['files'] ?? false) || ($settings['js']['files'] ?? false)) {
                    /** @var HttpResponseEx $response */
                    $response = $event->getResponse();

                    try {
                        if ($content = $response->getContent()) {
                            $this->version    = ($settings['version'] ?? 0) ?: 0.01;
                            $this->jsMinifier = $settings['jsMinifier'] ?? false;

                            $html5  = new HTML5();
                            $dom    = $html5->loadHTML($content);
                            $assets = $delete = $sources = [];

                            $host = $this->config->getPublicVars('host');
                            $min  = function ($url) { return stripos($url, '.min.') !== false; };
                            $url  = function ($url) use ($host) {
                                $url = preg_match('~^//~', $url) ? "https:$url" : $url;
                                $url = filter_var($url, FILTER_VALIDATE_URL) ? $url : sprintf("%s/%s", $host, ltrim($url, '/'));
                                $url = sprintf("%s%scacheBuster=%s", $url, strpos('?', $url) !== false ? '&' : '?', rand(11111, 99999999999999));

                                return $url;
                            };

                            if ($settings['js']['files'] ?? false) {
                                /** @var \DOMElement $item */
                                foreach ($dom->getElementsByTagName('script') as $item) {
                                    if (($src = $item->getAttribute('src')) && !preg_match('~/cache/~', $src)) {
                                        $assets['js'][] = new HttpAsset($url($src), [new JSConcatFilter()], true);;
                                        $sources['js'][] = $src;
                                        $delete['js'][]  = $item;
                                    }
                                }
                            }

                            if ($settings['css']['files'] ?? false) {
                                foreach ($dom->getElementsByTagName('link') as $item) {
                                    if (($item->getAttribute('rel') == 'stylesheet') && ($src = $item->getAttribute('href')) && !preg_match('~/cache/~', $src)) {
                                        $assets['css'][] = new HttpAsset($url($src), array_merge([new CssImportFilter(), new CssRewriteFilter()], !$min($src) ? [new CssMinFilter()] : []), true);;
                                        $sources['css'][] = $src;
                                        $delete['css'][]  = $item;
                                    }
                                }
                            }

                            foreach ($assets as $type => $urls) {
                                $name  = sprintf('%s.%s', md5(json_encode($sources[$type])), $type);
                                $found = MMinifiedDatum::where('name', '=', $name)->where('version', '=', $this->version)->count();

                                if (!$found) {
                                    $asset = new AssetCollection($urls);
                                    $asset->setTargetPath(sprintf('/static/cache/%s/', $type));

                                    if ($contents = $asset->dump()) {
                                        if ($type === 'css') { //hack
                                            $contents = preg_replace('~http://~i', '//', $contents);
                                        }

                                        if (MMinifiedDatum::create(['name' => $name, 'version' => (string) $this->version, 'content' => $contents, 'created_at' => Carbon::now()])) {
                                            $found = true;
                                        }
                                    }
                                }

                                if ($found) {
                                    foreach ($delete[$type] as $item) {
                                        $item->parentNode->removeChild($item);
                                    }

                                    $url = sprintf("%s/static/cache/%s/%s", preg_replace('/^https?:/', '', $host), $this->version, $name);

                                    if ($type == 'css') {
                                        $tag = $dom->createElement('link');
                                        $tag->setAttribute('href', $url);
                                        $tag->setAttribute('rel', 'stylesheet');
                                        $dom->getElementsByTagName('head')->item(0)->appendChild($tag);
                                    } else {
                                        $tag = $dom->createElement('script');
                                        $tag->setAttribute('src', $url);

                                        if ($script = $dom->getElementsByTagName('script')->item(0)) {
                                            $script->parentNode->insertBefore($tag, $script);
                                        } else {
                                            $dom->getElementsByTagName('body')->item(0)->appendChild($tag);
                                        }
                                    }
                                }
                            }

                            $response->setContent($dom->saveHTML());
                        }
                    } catch (\Exception $e) {
                        $this->logger->error("Minify error: " . $e->getMessage());
                    }
                }
            }
        }
    }
}