<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/5/2016
 * Time: 11:04 AM
 */
namespace Minute\Todo {

    use Minute\Config\Config;
    use Minute\Event\ImportEvent;

    class MinifyTodo {
        /**
         * @var TodoMaker
         */
        private $todoMaker;
        /**
         * @var Config
         */
        private $config;

        /**
         * MailerTodo constructor.
         *
         * @param TodoMaker $todoMaker - This class is only called by TodoEvent (so we assume TodoMaker is be available)
         * @param Config $config
         */
        public function __construct(TodoMaker $todoMaker, Config $config) {
            $this->todoMaker = $todoMaker;
            $this->config    = $config;
        }

        public function getTodoList(ImportEvent $event) {
            $todos[] = ['name' => 'Enable minifier for CSS', 'description' => '', 'status' => $this->config->get('minify/css/files') ? 'complete' : 'incomplete', 'link' => '/admin/minify'];
            $todos[] = ['name' => 'Enable minifier for Javascript', 'description' => '', 'status' => $this->config->get('minify/js/files') ? 'complete' : 'incomplete', 'link' => '/admin/minify'];

            $event->addContent(['Minifier' => $todos]);
        }
    }
}