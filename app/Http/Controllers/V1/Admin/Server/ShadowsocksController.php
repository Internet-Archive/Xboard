<?php

namespace App\Http\Controllers\V1\Admin\Server;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServerShadowsocksSave;
use App\Http\Requests\Admin\ServerShadowsocksUpdate;
use App\Models\ServerShadowsocks;
use Illuminate\Http\Request;

class ShadowsocksController extends Controller
{
    public function save(ServerShadowsocksSave $request)
    {
        $params = $request->validated();
        if ($request->input('id')) {
            $server = ServerShadowsocks::find($request->input('id'));
            if (!$server) {
                throw new ApiException(500, '服务器不存在');
            }
            try {
                $server->update($params);
            } catch (\Exception $e) {
                throw new ApiException(500, '保存失败');
            }
            return response([
                'data' => true
            ]);
        }

        if (!ServerShadowsocks::create($params)) {
            throw new ApiException(500, '创建失败');
        }

        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        if ($request->input('id')) {
            $server = ServerShadowsocks::find($request->input('id'));
            if (!$server) {
                throw new ApiException(500, '节点ID不存在');
            }
        }
        return response([
            'data' => $server->delete()
        ]);
    }

    public function update(ServerShadowsocksUpdate $request)
    {
        $params = $request->only([
            'show',
        ]);

        $server = ServerShadowsocks::find($request->input('id'));

        if (!$server) {
            throw new ApiException(500, '该服务器不存在');
        }
        try {
            $server->update($params);
        } catch (\Exception $e) {
            throw new ApiException(500, '保存失败');
        }

        return response([
            'data' => true
        ]);
    }

    public function copy(Request $request)
    {
        $server = ServerShadowsocks::find($request->input('id'));
        $server->show = 0;
        if (!$server) {
            throw new ApiException(500, '服务器不存在');
        }
        if (!ServerShadowsocks::create($server->toArray())) {
            throw new ApiException(500, '复制失败');
        }

        return response([
            'data' => true
        ]);
    }
}
