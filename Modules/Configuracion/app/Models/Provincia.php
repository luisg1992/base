<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'Provincias';

    protected $primaryKey = 'IdProvincia';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdDepartamento',
        'IdReniec',
    ];

    protected $casts = [
        'IdProvincia' => 'int',
        'IdDepartamento' => 'int',
        'IdReniec' => 'int',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'IdDepartamento', 'IdDepartamento');
    }
}
