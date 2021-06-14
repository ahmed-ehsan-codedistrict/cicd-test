<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ORTPMS0 extends BaseModel
{
    public $table = 'ORTPMS0';

    protected $fillable = ['CONO3Q','TPCD3Q','TPDS3Q',
                            'RCRQ3Q','ORPR3Q','RQBK3Q',
                            'ALPK3Q','ALRE3Q','APCL3Q',
                            'FRPT3Q','INTP3Q','WHNO3Q',
                            'LCCD3Q','STCD3Q','CSEX3Q',
                            'FGTP3Q','SLAC3Q','SLDP3Q',
                            'SLSA3Q','TDAC3Q','TDDP3Q',
                            'TDSA3Q','SHAC3Q','SHDP3Q',
                            'SHSA3Q','TXAC3Q','TXDP3Q',
                            'TXSA3Q','COAC3Q','CODP3Q',
                            'COSA3Q','DAAC3Q','DADP3Q',
                            'DASA3Q'];
}
