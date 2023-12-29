<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var File $resource */
        $resource = $this->resource;

        return [
          'id' => $resource->id,
          'name' => $resource->name,
          'serverPath' => $resource->serverPath,
          'project_id' => $resource->project_id,
          'folder_id' => $resource->folder_id,
          'checkedBy' => User::find($resource->checked_in_by)?->username ?? null,
        ];
    }
}
