<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Minute\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Filters assets through JsMin.
 *
 * All credit for the filter itself is mentioned in the file itself.
 *
 * @link   https://raw.github.com/mrclay/minify/master/min/lib/JSMin.php
 * @author Brunoais <brunoaiss@gmail.com>
 */
class JSConcatFilter implements FilterInterface {
    public function filterLoad(AssetInterface $asset) {
    }

    public function filterDump(AssetInterface $asset) {
        $asset->setContent(sprintf("\n//Start concat\n\n%s\n;;\n\n//End concat\n\n", $asset->getContent()));
    }
}
