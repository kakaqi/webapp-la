<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    protected $guard = 'web';
    public function __construct()
    {
        Auth::guard($this->guard);
    }
    /**
     * 登录页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        if ( Auth::user() ) {
            return redirect()->intended('/admin/index');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_name' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return [
                'code'=>4001,
                'text'=> $validator->errors()->first(),
                'result'=>'',
            ];
        }

        $name = $request->input('user_name');
        $password = $request->input('password');
        $data  = [
            'name' => $name,
            'password' => $password,
        ];

        // 验证失败返回403
        if (! $token = Auth::attempt($data)) {
            return [
                'code'=>4003,
                'text'=>'帐号或密码错误！',
                'result'=>'',
            ];
        }
        $user_info = User::where('name', $name)->first();

        return [
          'code' => 0,
          'text' => 'success',
          'result' => $user_info
        ];
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->intended('/admin/login');
    }
}
