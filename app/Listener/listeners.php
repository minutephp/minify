<?php

/** @var Binding $binding */
use Minute\Docker\UglifyJs;
use Minute\Event\AdminEvent;
use Minute\Event\Binding;
use Minute\Event\DockerEvent;
use Minute\Event\ResponseEvent;
use Minute\Event\TodoEvent;
use Minute\Menu\MinifyMenu;
use Minute\Minify\Minify;
use Minute\Todo\MinifyTodo;

$binding->addMultiple([
    //minify
    ['event' => AdminEvent::IMPORT_ADMIN_MENU_LINKS, 'handler' => [MinifyMenu::class, 'adminLinks']],

    ['event' => ResponseEvent::RESPONSE_RENDER, 'handler' => [Minify::class, 'minify'], 'priority' => -1],

    //tasks
    ['event' => TodoEvent::IMPORT_TODO_ADMIN, 'handler' => [MinifyTodo::class, 'getTodoList']],

    //uglify
    ['event' => DockerEvent::DOCKER_INCLUDE_FILES, 'handler' => [UglifyJs::class, 'docker']],
]);