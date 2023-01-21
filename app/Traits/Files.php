<?php

namespace App\Traits;


trait Files
{
    /**
     * Fetch image
     *
     * @return string|null
     */
    public function fileUrl()
    {
        if (file_exists($this->image)) {
            return url($this->image);
        }

        $default = 'images/default.png';
        if (file_exists($default)) {
            return url($default);
        }

        return null;
    }

    /**
     * Store image
     *
     * @return string|null
     */
    public function storeFile()
    {
        if (request()->file('image')) {
            $image = request()->file('image');
            $imageName = time() . '.' . $image->extension();

            $image->move(public_path('images'), $imageName);

            return 'images/' . $imageName;
        }

        return null;
    }

    /**
     * Update image
     *
     * @return string|null
     */
    public function updateFile()
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = 'images/' . time() . '.' . $image->extension();

            if (file_exists($this->image)) {
                @unlink($this->image);
            }
            $image->move(public_path('images'), $imageName);

            return $imageName;
        }

        return $this->image ?? null;
    }

    /**
     * Delete image
     *
     * @return void
     */
    public function deleteFile()
    {
        if (file_exists($this->image)) {
            @unlink($this->image);
        }
    }
}
