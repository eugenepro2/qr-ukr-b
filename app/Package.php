<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'from_project',
        'to_project'
    ];

    public function fromProject()
    {
        return $this->belongsTo(Project::class, 'from_project', 'id');
    }

    public function toProject()
    {
        return $this->belongsTo(Project::class, 'to_project', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
