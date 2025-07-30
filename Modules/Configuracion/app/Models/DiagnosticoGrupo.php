<?php

namespace Modules\Configuracion\Models;



use Illuminate\Database\Eloquent\Model;

class DiagnosticoGrupo extends Model
{
    protected $table = 'DiagnosticosGrupos';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'IdGrupo';
    protected $keyType = 'int';

    protected $fillable = [
        'Descripcion',
        'IdCapitulo',
    ];
    public function diagnosticocapitulo()
    {
        return $this->belongsTo(DiagnosticoCapitulo::class, 'IdCapitulo', 'IdCapitulo');
    }
}