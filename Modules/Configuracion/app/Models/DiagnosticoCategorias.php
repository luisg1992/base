<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;


class DiagnosticoCategorias extends Model
{
    protected $table = 'DiagnosticosCategorias';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'IdCategoria';
    protected $keyType = 'int';

    protected $fillable = [
        'Codigo',
        'Descripcion',
        'IdGrupo',
    ];
    public function diagnosticogrupo()
    {
        return $this->belongsTo(DiagnosticoGrupo::class, 'IdCapitulo', 'IdCapitulo');
    }
}