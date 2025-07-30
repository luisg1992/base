<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'Distritos';

    protected $primaryKey = 'IdDistrito';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdProvincia',
        'IdReniec',
    ];

    protected $casts = [
        'IdDistrito' => 'int',
        'IdProvincia' => 'int',
        'IdReniec' => 'int',
    ];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'IdProvincia', 'IdProvincia');
    }
}
