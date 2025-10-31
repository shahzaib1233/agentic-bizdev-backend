<?php

namespace App\Models\Agents;

use Illuminate\Database\Eloquent\Model;

class AgentsModel extends Model
{
    //

    protected $table = "agent";
    protected $fillable = [
        "name",
        "description"
    ];
}
