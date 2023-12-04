<?php

namespace App\Http\Controllers\V1\Admin\Server;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\ServerHysteria;
use Illuminate\Http\Request;

class HysteriaController extends Controller
{
    public function save(Request $request)
    {
        $params = $request->validate([
            'show' => '',
            'name' => 'required',
            'group_id' => 'required|array',
            'route_id' => 'nullable|array',
            'parent_id' => 'nullable|integer',
            'host' => 'required',
            'port' => 'required',
            'server_port' => 'required',
            'tags' => 'nullable|array',
            'excludes' => 'nullable|array',
            'ips' => 'nullable|array',
            'rate' => 'required|numeric',
            'up_mbps' => 'required|numeric|min:1',
            'down_mbps' => 'required|numeric|min:1',
            'server_name' => 'nullable',
            'insecure' => 'required|in:0,1',
            'alpn' => 'nullable|in:0,1,2,3',
            'version' => 'nullable|in:1,2',
            'is_obfs' => 'nullable'
        ],[
            'name.required' => '节点名称不能为空',
            'group_id.required' => '权限组不能为空',
            'host.required' => '节点地址不能为空',
            'port.required' => '连接端口不能为空',
            'server_port' => '服务端口不能为空',
            'rate.required' => '倍率不能为空',
            'up_mbps.required' => '上行带宽不能为空',
            'down_mbps.required' => '下行带宽不能为空',
        ]);

        if ($request->input('id')) {
            $server = ServerHysteria::find($request->input('id'));
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

        if (!ServerHysteria::create($params)) {
            throw new ApiException(500, '创建失败');
        }

        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        if ($request->input('id')) {
            $server = ServerHysteria::find($request->input('id'));
            if (!$server) {
                throw new ApiException(500, '节点ID不存在');
            }
        }
        return response([
            'data' => $server->delete()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'show' => 'in:0,1'
        ], [
            'show.in' => '显示状态格式不正确'
        ]);
        $params = $request->only([
            'show',
        ]);

        $server = ServerHysteria::find($request->input('id'));

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
        $server = ServerHysteria::find($request->input('id'));
        $server->show = 0;
        if (!$server) {
            throw new ApiException(500, '服务器不存在');
        }
        if (!ServerHysteria::create($server->toArray())) {
            throw new ApiException(500, '复制失败');
        }

        return response([
            'data' => true
        ]);
    }
}
