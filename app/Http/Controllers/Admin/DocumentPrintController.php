<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\DocumentPrint;
use Illuminate\Http\Request;
use Str;

class DocumentPrintController extends Controller
{
    public function check_can_print(Request $request)
    {
        $document_print_helper = new PrintHelper();
        $result = $document_print_helper->check_can_print(
            $request->model,
            $request->model_id,
            $request->print_type,
        );

        return response()->json([
            'data' => $result,
        ]);
    }

    public function request_print(Request $request)
    {
        try {
            $document_print_helper = new PrintHelper();
            $result = $document_print_helper->print_request(
                $request->model,
                $request->model_id,
                $request->reason,
                $request->link,
                $request->export_link,
                $request->print_type,
                $request->type,
                auth()->user()->branch_id,
                "Pengajuan Print " . Str::headline($request->print_type),
                Str::headline($request->print_type) . " No. " . $request->code,
            );

            $result = json_decode($result->getContent());
            return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => $th->getMessage(),
            ]);
        }
    }

    public function get_print_request_approval(Request $request)
    {
        $document_print = DocumentPrint::with(['document_print_approvals'])
            ->where('model', $request->model)
            ->where('model_id', $request->model_id)
            ->where('type', $request->type)
            ->orderBy('id', 'desc')
            ->first();

        $data['view'] = view('components.print_request', [
            'document_print' => $document_print,
        ])->render();

        return response()->json($data);
    }

    public function authorize_request_print($id, Request $request)
    {
        $document_print_helper = new PrintHelper();
        $result = $document_print_helper->authorize($id, $request->status, $request->message);

        return redirect()->back()->with($result);
    }
}
