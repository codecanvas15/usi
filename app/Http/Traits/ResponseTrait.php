<?php

namespace App\Http\Traits;

trait ResponseTrait
{

    public function AjaxResponse(
        bool $success = true,
        string $method = 'create',
        string $message = null,
        string $exception_message = null,
        int $code = 200,
        mixed $data = null,
        string $custom_message = null,
    ) {
        if ($success) {
            $final_message = 'Berhasil ';
        } else {
            $final_message = 'Gagal ';
        }

        if ($method == 'create') {
            $final_message .= 'menyimpan data baru. ';
        } elseif ($method == 'edit') {
            $final_message .= 'memperbarui data. ';
        } elseif ($method == 'delete') {
            $final_message .= 'menghapus data. ';
        }

        if ($message != null) {
            $final_message .= $message . ' ';
        }

        if ($exception_message != null) {
            $final_message .= $exception_message;
        }

        if ($custom_message) {
            $final_message = $custom_message;
        }

        if ($data == null) {
            return response()->json(['is_success' => $success, 'method' => $method, 'message' => $final_message], $code);
        } else {
            return response()->json(['is_success' => $success, 'method' => $method, 'message' => $final_message, 'result' => $data]);
        }
    }

    public function Response(
        bool $success = true,
        string $method = 'create',
        string $message = null,
        string $exception_message = null,
        array $data = [],
    ) {
        if ($success) {
            $final_message = 'Berhasil ';
        } else {
            $final_message = 'Gagal ';
        }

        if ($method == 'create') {
            $final_message .= 'menyimpan data baru. ';
        } elseif ($method == 'edit') {
            $final_message .= 'memperbarui data. ';
        } elseif ($method == 'delete') {
            $final_message .= 'menghapus data. ';
        }

        if ($message != null) {
            $final_message .= $message . ' ';
        }

        if ($exception_message != null) {
            $final_message .= $exception_message;
        }

        return ['is_success' => $success, 'method' => $method, 'message' => $final_message, 'data' => $data];
    }
}
