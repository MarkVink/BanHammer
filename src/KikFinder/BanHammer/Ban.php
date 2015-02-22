<?php

namespace KikFinder\BanHammer;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    protected $table   = "banhammer_bans";
    protected $guarded = ['id'];
}