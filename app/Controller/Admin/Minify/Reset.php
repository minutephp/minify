<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin\Minify {

    use App\Model\MConfig;
    use Minute\View\Redirection;

    class Reset {

        public function index() {
            MConfig::where('type', '=', 'minify')->delete();

            return new Redirection('/admin');
        }
    }
}