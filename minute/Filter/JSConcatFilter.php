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

class JSConcatFilter implements FilterInterface {
    public function filterLoad(AssetInterface $asset) {
    }

    public function filterDump(AssetInterface $asset) {
        $asset->setContent(sprintf("\n;;\n\n%s\n\n;;\n", $asset->getContent()));
    }
}
