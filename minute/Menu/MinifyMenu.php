<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 7:57 PM
 */
namespace Minute\Menu {

    use Minute\Event\ImportEvent;

    class MinifyMenu {
        public function adminLinks(ImportEvent $event) {
            $links = [
                'minify' => ['title' => 'Minify', 'icon' => 'fa-compress', 'priority' => 70, 'parent' => 'expert', 'href' => '/admin/minify']
            ];

            $event->addContent($links);
        }
    }
}