<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Book List
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $books = Book::where('status', 'active')->orderByDesc('id');

        if ($request->filled('status')) {
            $books->where('status', strtolower($request->status));
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $books->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orwhere('status', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $this->response([
            'data' => BookResource::collection($books->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($books->paginate($configs['rows_per_page'])->appends($request->all()))
        ]);
    }

    /**
     * View Book
     *
     * @param int $id
     * @return json
     */
    public function view($id)
    {
        $book = Book::findOrFail($id);

        return $this->response(['data' => new BookResource($book)]);
    }
}
