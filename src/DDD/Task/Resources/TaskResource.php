<?php

namespace DDD\Task\Resources;

use DDD\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'status' => $this->resource->status,
            'assigned_to' => $this->resource->assigned_to,
            'due_date' => $this->resource->due_at?->toDateString(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
            'assignee' => $this->whenLoaded('assignee', function () {
                return $this->resource->assignee
                    ? [
                        'id' => $this->resource->assignee->id,
                        'name' => $this->resource->assignee->name,
                        'email' => $this->resource->assignee->email,
                    ]
                    : null;
            }),
        ];
    }
}
