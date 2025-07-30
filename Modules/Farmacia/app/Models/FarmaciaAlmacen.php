<?php

namespace Modules\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

class FarmaciaAlmacen extends Model
{
    protected $table = 'farmAlmacen';
    protected $primaryKey = 'idAlmacen';

    public function scopeWhereActivo($query)
    {
        return $query->where('idEstado', 1);
    }
}
