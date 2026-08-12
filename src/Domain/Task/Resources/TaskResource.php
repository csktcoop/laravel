<?php

namespace Domain\Task\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'due_date' => $this->due_date?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'assignee' => $this->whenLoaded('assignee', function () {
                return $this->assignee
                    ? [
                        'id' => $this->assignee->id,
                        'name' => $this->assignee->name,
                        'email' => $this->assignee->email,
                    ]
                    : null;
            }),
        ];
    }
}
