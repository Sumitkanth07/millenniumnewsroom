<?php

if (!function_exists('uniqueFileName')) {
    function uniqueFileName($file): string
    {
        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $extension = $file->getClientOriginalExtension();

        return time() . '_' .
               uniqid() . '_' .
               \Illuminate\Support\Str::slug($originalName) .
               '.' . $extension;
    }
}