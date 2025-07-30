<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $table = 'Impresoras';

    protected $primaryKey = 'ImpresorasId';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdTerminales',
        'Nombre',
        'Estado',
        'PorDefecto',
        'Formato'
    ];

    protected $casts = [
        'IdTerminales' => 'int',
        'Estado' => 'int',
        'PorDefecto' => 'int',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'IdTerminales', 'TerminalesId');
    }
}
