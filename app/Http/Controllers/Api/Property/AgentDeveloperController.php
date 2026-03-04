<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\AgentResource;
use App\Http\Resources\Model\DeveloperResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Agent;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentDeveloperController extends Controller
{
    use GeneralTrait;
    public function allAgents(Request $request)
    {
        try {
            $query = Agent::with(['properties:id,agent_id,title'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                });

            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $agents = $query->paginate($perPage, ['*'], 'page', $page);

            return $this->apiResponse([
                'agents' => AgentResource::collection($agents),
                'pagination' => [
                    'current_page'   => $agents->currentPage(),
                    'requested_page' => (int) $page,
                    'per_page'       => $agents->perPage(),
                    'total'          => $agents->total(),
                    'last_page'      => $agents->lastPage(),
                    'next_page_url'  => $agents->nextPageUrl(),
                    'prev_page_url'  => $agents->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


public function allDevelopers(Request $request)
{
    try {
        $developers = DB::table('developers')
            ->select([
                'id',
                'name',
                'slug',
                'email',
                'contact_inf',
                'logo',
                'thumbnail',
                'description',
                'description_top',
                'description_bottom',
                'active',
                'created_at',
                'updated_at',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            })
            ->where('active', 1)
            ->get();

        return $this->apiResponse([
            'developers' => $developers, // same structure
        ]);
    } catch (\Exception $e) {
        return $this->handleException($e);
    }
}

    public function show(Request $request)
    {
        try {
            $developer = Developer::findOrFail($request->devolper_id);
            return $this->apiResponse([
                'developer' => new \App\Http\Resources\Model\DeveloperResource($developer),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


}
