<?php

namespace App\Models;

class Translation extends BaseModel
{

    protected $table = 'translations';

    public function userInfo()
    {
        return $this->belongsTo(Wxuser::class, 'openId', 'openId');
    }
}
