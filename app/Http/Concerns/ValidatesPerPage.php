<?php

namespace App\Http\Concerns;

use Illuminate\Http\Request;

trait ValidatesPerPage
{
    /**
     * Kembalikan nilai per_page yang aman dari request.
     *
     * @param  Request  $request
     * @param  int      $default  Nilai default jika tidak ada di request
     * @param  int      $max      Batas maksimum yang diizinkan
     * @return int
     */
    protected function getPerPage(Request $request, int $default = 15, int $max = 100): int
    {
        $perPage = (int) $request->get('per_page', $default);

        // Jika nilai tidak valid atau terlalu besar, gunakan default
        if ($perPage < 1 || $perPage > $max) {
            return $default;
        }

        return $perPage;
    }
}
