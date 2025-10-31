<?php

namespace App\Http\Controllers\Agents;

use App\Http\Controllers\Controller;
use App\Models\Agents\AgentsModel;
use Illuminate\Http\Request;

class AgentsController extends Controller
{
    //
    public function GetAllAgents()
    {
        $agents = AgentsModel::get();
        if ($agents->isNotEmpty()) {
            return response()->json(
                [
                    'message' => 'Agents Found Successfully',
                    'Agents' => $agents,
                ]
            );
        }

        return response()->json(
            [
                'message' => 'No agents found',
                'Agents' => [],
            ],
            404
        );
    }

    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|unique:agent,name',
            'description' => 'required|string',
        ]);

        $agent = AgentsModel::create($fields);

        if (! $agent) {
            return response()->json([
                'message' => 'Agent not created successfully',
            ], 400);
        }

        return response()->json([
            'message' => 'Agent created successfully',
            'data' => $agent,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $agent = AgentsModel::find($id);

        if (! $agent) {
            return response()->json([
                'message' => 'Agent not found',
            ], 404);
        }

        $fields = $request->validate([
            'name' => 'sometimes|string|unique:agent,name,'.$id,
            'description' => 'sometimes|string',
        ]);

        $agent->update($fields);

        return response()->json([
            'message' => 'Agent updated successfully',
            'data' => $agent,
        ]);
    }

    public function destroy($id)
    {
        $agent = AgentsModel::find($id);

        if (! $agent) {
            return response()->json([
                'message' => 'Agent not found',
            ], 404);
        }

        $agent->delete();

        return response()->json([
            'message' => 'Agent deleted successfully',
        ]);
    }
}
