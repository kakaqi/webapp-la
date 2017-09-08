<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function __construct()
    {
        var_export(11);die;
    }
    public function index(Request $request)
    {

        $this->view_data['tasks'] = [
            [
                'name' => 'Design New Dashboard',
                'progress' => '87',
                'color' => 'danger'
            ],
            [
                'name' => 'Create Home Page',
                'progress' => '76',
                'color' => 'warning'
            ],
            [
                'name' => 'Some Other Task',
                'progress' => '32',
                'color' => 'success'
            ],
            [
                'name' => 'Start Building Website',
                'progress' => '56',
                'color' => 'info'
            ],
            [
                'name' => 'Develop an Awesome Algorithm',
                'progress' => '10',
                'color' => 'success'
            ]
        ];
        $this->view_data['page_title'] = '首页';
        $this->view_data['page_name'] = '首页';
        return view('admin.index')->with($this->view_data);
    }
}
