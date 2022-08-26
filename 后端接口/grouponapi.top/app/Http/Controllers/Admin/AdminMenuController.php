<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\AdminMenu;

class AdminMenuController extends BaseController
{
    /**
     * 后台管理菜单列表
     */
    public function index()
    {
        $list=AdminMenu::select('id','name','pid','level')
        ->where('status',1)
        ->where('pid',0)
        ->with('children:id,name,pid,level')
        ->get();
        return $list;
    }
}
