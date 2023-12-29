<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Folder;

class FolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Folder $resource */
        $resource = $this->resource;
        
        return [
            'id' => $resource->id,
            'project_id' => $resource->project_id,
            'folder_id' => $resource->folder_id,
        ];
    }
}
