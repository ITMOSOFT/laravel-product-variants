<?php

namespace ItmoSoft\ProductVariants\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\ProductVariants\Concerns\HasProductVariants;

class Product extends Model
{
    use HasProductVariants;

    protected $table = 'products';
    protected $guarded = [];
}
