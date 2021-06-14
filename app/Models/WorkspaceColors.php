<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceColors extends BaseModel
{
    public $table = 'WorkspaceColors';

    protected $guarded = [];

    public function COLRMS0()
    {
        return $this->belongsToMany('App\Models\COLRMS0','WorkspaceColors','ColorId','CRCD3J');
    }

    public function Workspace()
    {
        return $this->belongsToMany('App\Models\Workspaces', 'WorkspaceColors','WorkspaceId','Id');
    }

    public static function deleteWorkspace($wid)
    {
        $workspace = WorkspaceColors::where('WorkspaceId','=',$wid)->delete();
    }
}
