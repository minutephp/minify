<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin\Minify {

    use App\Model\MMinifiedDatum;
    use Minute\Cache\QCache;

    class Truncate {
        /**
         * @var QCache
         */
        private $cache;

        /**
         * Truncate constructor.
         *
         * @param QCache $cache
         */
        public function __construct(QCache $cache) {
            $this->cache = $cache;
        }

        public function index() {
            $this->cache->flush();

            MMinifiedDatum::truncate();

            return 'ok';
        }
    }
}