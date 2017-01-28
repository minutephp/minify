<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 1/26/2017
 * Time: 10:53 AM
 */
namespace Minute\Docker {

    use Minute\Event\DockerEvent;

    class UglifyJs {
        public function docker(DockerEvent $event) {
            $event->addContent('Dockerfile', 'RUN npm install uglify-js -g');
        }
    }
}