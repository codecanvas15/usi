<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\OfferingLetter as model;
use App\Models\OfferingLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OfferingLetterController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    public $view_folder = "offering-letter";

    public function show(Request $request)
    {
        $id = Hashids::decode($request->id)[0];
        $offering_letter = OfferingLetter::findOrFail($id);
        if (Carbon::now()->lt(Carbon::parse($offering_letter->due_date)) || $offering_letter->applicant_status != 'pending') {
            return abort(404);
        }

        $data['offering_letter'] = $offering_letter;
        $data['document_link'] = route('guest.offering-letter.document', ['id' => Hashids::encode($offering_letter->id)]);

        return view("guest.$this->view_folder.create", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = \App\Models\OfferingLetter::findOrFail($id);

        $model->fill([
            'applicant_status' => $request->applicant_status,
            'applicant_status_at' => Carbon::now(),
            'applicant_status_reason' => $request->reason,
        ]);

        try {
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        return redirect()->route('guest.offering-letter.success');
    }

    public function document($id)
    {
        $id = Hashids::decode($id)[0];
        $model = model::findOrFail($id);
        $file = public_path('/pdf_reports/Offering-Letter-' . microtime(true) . '.pdf');
        $fileName = 'Offering-Letter-' . microtime(true) . '.pdf';

        $pdf = Pdf::loadview("admin/.$this->view_folder./export", compact('model'))->setPaper('a4', 'portrait');
        $pdf->render();

        return $pdf->stream($fileName);
    }

    function success()
    {
        return view("guest.$this->view_folder.success");
    }
}
