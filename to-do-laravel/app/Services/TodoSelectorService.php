<?php

namespace App\Services;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

class TodoSelectorService
{
    private Todo $todoModel;


    public array $categories = ['work', 'home'];

    public function __construct(Todo $todoModel)
    {
        $this->todoModel = $todoModel;
    }

    public function selectTodos($status, $category): Collection
    {

        $todos = $this->todoModel->with('category');

        if (!is_null($status)) {
            $todos = $todos->where('completed', '=', $status);
        }

        if (!is_null($category)) {
            $todos = $todos->whereRelation('category', 'name', '=', $category);
        }

        return $todos->get();
    }
}


