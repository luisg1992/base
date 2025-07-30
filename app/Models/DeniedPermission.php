<?php

use Illuminate\Database\Eloquent\Model;

class DeniedPermission extends Model
{
    protected $table = 'DeniedPermissions'; // Nombre de la tabla
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'permission_id'
    ];
}
