<?php

namespace App\Http\Controllers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    /**
     * Index.
     *
     * @param Request $request Request
     *
     * @return bool|Repository|Application|ResponseFactory|Response|mixed|string|void
     */
    public function index(Request $request)
    {
        $action = trim(strtolower($request->get('action')));
        switch ($action) {
            case 'config':
                $type = $request->get('type', 'custom');
                return config($type);
            case 'phpinfo':
                return phpinfo();
            case 'math':
                $formular = $request->get('formular');
                $formular = replaceAll(' ', '+', $formular);
                foreach (['+', '-', '*', "/"] as $phep) {
                    if (str_contains($formular, $phep)) {
                        $parser = explode($phep, $formular);
                        $parser = array_map('trim', $parser);
                        $formular = implode(' ' . $phep . ' ', $parser);
                    }
                }
                return $formular . ' = ' . shell_exec('echo "scale=2;' . $formular . '"|bc');
            case 'command':
                $command = $request->get('command');
                Artisan::queue($command);
                return 'done';
            case 'redis':
                try {
                    Redis::connection();
                } catch (\Exception $e) {
                    return response($e->getMessage());
                }
                return 'Redis working';
            case 'cache-clear':
                $tag = $request->get('tag');
                if (!empty($tag)) {
                    Cache::tags([$tag])->flush();
                    return 'done';
                }
                Cache::flush();
                return 'done';
            case 'mail':
                $email = $request->get('email', 'loc.nd@neo-lab.vn');
                Mail::raw('Test send mail', function ($message) use ($email) {
                    $message->to($email)->subject(config('app.name') . ' - Testing');
                });
                return 'done';
        }
        abort(404);
    }
}
