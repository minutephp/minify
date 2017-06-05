<?php
/**
 * Created by: MinutePHP framework
 */

namespace App\Controller\Minify {

    use App\Model\MMinifiedDatum;
    use Minute\Cache\QCache;
    use Minute\Http\HttpResponseEx;

    class Content {
        /**
         * @var HttpResponseEx
         */
        private $response;
        /**
         * @var QCache
         */
        private $cache;

        /**
         * Content constructor.
         *
         * @param QCache $cache
         * @param HttpResponseEx $response
         */
        public function __construct(QCache $cache, HttpResponseEx $response) {
            $this->response = $response;
            $this->cache    = $cache;
        }

        public function index(string $name, string $version) {
            $content = $this->cache->get("minify-$name-$version", function () use ($name, $version) {
                if ($record = MMinifiedDatum::where('name', '=', $name)->where('version', '=', $version)->first()) {
                    return $record['content'];
                }

                return null;
            });

            if (!empty($content)) {
                $this->response->setFinal(true);
                $this->response->setStatusCode(200);
                $this->response->setHeader('Content-Type', preg_match('/\.js$/', $name) ? 'application/javascript' : 'text/css');
                $this->response->setHeader('Cache-Control', 'max-age=31622400, public');
                $this->response->setHeader('Access-Control-Allow-Origin', "*");
                $this->response->setHeader('Access-Control-Allow-Headers', "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
                $this->response->setHeader('Access-Control-Allow-Methods', "POST, GET, OPTIONS");

                $this->response->setContent($content);
            } else {
                $this->response->setStatusCode(404);
            }

            return $this->response;
        }
    }
}