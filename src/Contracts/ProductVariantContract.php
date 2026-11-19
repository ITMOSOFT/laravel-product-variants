<?php

namespace ItmoSoft\ProductVariants\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Contract shared by variant models backed by different product packages.
 *
 * @mixin Model
 */
interface ProductVariantContract
{
    public function product(): Relation;
}
