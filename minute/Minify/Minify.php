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
    use Assetic\Filter\CssImportFilter;
    use Assetic\Filter\CssMinFilter;
    use Assetic\Filter\CssRewriteFilter;
    use Assetic\Filter\UglifyJs2Filter;
    use Assetic\Filter\UglifyJsFilter;
    use Carbon\Carbon;
    use Minute\Cache\QCache;
    use Minute\Config\Config;
    use Minute\Debug\Debugger;
    use Minute\Event\ResponseEvent;
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
         * @var string
         */
        protected $uglify;
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
        }

        public function minify(ResponseEvent $event) {
            if ($event->isSimpleHtmlResponse()) {
                /** @var HttpResponseEx $response */
                $response = $event->getResponse();

                try {
                    if ($content = $response->getContent()) {
                        $settings = $this->cache->get('minify-settings', function () {
                            return $this->config->get(self::MINIFY_KEY);
                        }, 3600);

                        $this->version = ($settings['version'] ?? 0) ?: 0.01;
                        $this->uglify  = $settings['uglify'] ?: '/usr/local/bin/uglifyjs';

                        if (!empty($settings['css']['files'])) {
                            $content = $this->compress('#<lin' . 'k (?:.+)?href="(/static/(?!cache/)[^"]+\.css)"(?:.*)/?>(\r?\n)+?#', $content, 'css', $settings['css']['excludes'] ?? null);
                        }

                        if (!empty($settings['js']['files'])) {
                            $content = $this->compress('#<scrip' . 't (?:.+)?src="(/static/(?!cache/)[^"]+\.js)"(?:.*)>\s*</script>(\r?\n)+?#', $content, 'js', $settings['js']['excludes'] ?? null);
                        }

                        $response->setContent($content);
                    }
                } catch (\Exception $e) {
                    $this->logger->error("Minify error: " . $e->getMessage());
                }
            }
        }

        protected function compress($regex, $content, $type = 'js', $excludes) {
            $files = [];

            $compressed = preg_replace_callback($regex, function ($matches) use (&$files, $type, $excludes) {
                if (!empty($excludes) && preg_match("~$excludes~", basename($matches[1] ?? ''))) {
                    return $matches[0];
                }

                if (($type === 'css') && !preg_match('/rel="stylesheet"/i', $matches[0])) {
                    return $matches[0];
                } else {
                    $files[] = $matches[1];
                }

                return '';
            }, $content);

            if (!empty($files)) {
                MMinifiedDatum::unguard(true);

                $name   = sprintf('%s.%s', md5(json_encode($files)), $type);
                $count  = MMinifiedDatum::where('name', '=', $name)->where('version', '=', $this->version)->count();
                $uglify = null;

                if (empty($count)) {
                    set_time_limit(120);

                    if (file_exists($this->uglify)) {
                        $uglify = new UglifyJsFilter($this->uglify);
                        $uglify->setMangle(false);
                    }

                    $host  = $this->config->getPublicVars('host');
                    $asset = new AssetCollection(array_filter(array_map(function ($url) use ($type, $host, $uglify) {
                        $minified = preg_match('/\.min/', $url);

                        if ($type === 'css') {
                            $filters = array_merge([new CssImportFilter(), new CssRewriteFilter()], !$minified ? [new CssMinFilter()] : []);
                        } elseif (!$minified) {
                            $filters = !$minified && !empty($uglify) ? [$uglify] : [];//new JSMinFilter()]; //JSMinFilter breaks scripts sometimes :/
                        }

                        $url = filter_var($url, FILTER_VALIDATE_URL) ? $url : sprintf("%s/%s", $host, ltrim($url, '/'));
                        $url = sprintf("%s%scacheBuster=%s", $url, preg_match('/\?/', $url) ? '&' : '?', rand(11111, 99999999999999));

                        return new HttpAsset($url, $filters ?? [], true);
                    }, $files)));

                    $asset->setTargetPath(sprintf('/static/cache/%s/', $type));

                    $contents = $asset->dump();

                    if ($type === 'css') {
                        $contents = preg_replace('~http://~i', '//', $contents);
                    }

                    if (MMinifiedDatum::create(['name' => $name, 'version' => $this->version, 'content' => $contents, 'created_at' => Carbon::now()])) {
                        $count = 1;
                    }
                }
            }

            if (!empty($count) && !empty($name)) {
                $tag        = sprintf($type == 'js' ? '<scrip' . 't src="%s"></script>' : '<lin' . 'k href="%s" type="text/css" rel="stylesheet" media="all">', "/static/cache/$this->version/$name");
                $compressed = $type === 'css' ? preg_replace('#(<style|</head)#msi', "$tag\n\\1", $compressed, 1) : preg_replace('#(<script|</body)#msi', "$tag\n\\1", $compressed, 1);

                return $compressed;
            }

            return $content;
        }
    }
}