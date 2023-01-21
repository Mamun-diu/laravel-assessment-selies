<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookStoreRequest;
use App\Http\Requests\Admin\BookUpdateRequest;
use App\Http\Resources\Admin\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Book List
     *
     * @param Request $request
     * @return json $data
     */
    public function index(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $books = Book::orderByDesc('id');

        if ($request->filled('status')) {
            $books->where('status', strtolower($request->status));
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $books->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orwhere('status', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $this->response([
            'data' => BookResource::collection($books->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($books->paginate($configs['rows_per_page'])->appends($request->all()))
        ]);
    }

    /**
     * Store Book
     *
     * @param Request $request
     * @return json $data
     */
    public function store(BookStoreRequest $request)
    {
        $book = new Book;
        $request['image'] = $book->storeFile();

        if ($book->create($request->input())) {
            return $this->createdResponse([], 'The book has been successfully saved.');
        }

        return $this->errorResponse();
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

    /**
     * Update Book
     *
     * @param Request $request
     * @param int $id
     * @return json
     */
    public function update(BookUpdateRequest $request, $id)
    {
        $book = Book::findOrFail($id);

        $request['image'] = $book->updateFile();

        if ($book->update($request->input())) {
            return $this->successResponse(['message' => 'The book has been successfully saved.']);
        }

        return $this->errorResponse();
    }

    /**
     * Remove the specified book.
     *
     * @param $id
     * @return json
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $book->deleteFile();

        if ($book->delete()) {
            return $this->successResponse(['message' => 'The book has been successfully deleted.']);
        }

        return $this->errorResponse();
    }
}
