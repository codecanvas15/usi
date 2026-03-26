<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Branch;
use App\Models\LaborApplication;
use App\Models\LaborDemandDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaborApplicationController extends Controller
{
    protected $view_folder = 'labor-application';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $labor_demand_detail = LaborDemandDetail::findOrFail($request->labor_demand_detail_id);
        $data['labor_demand_detail'] = $labor_demand_detail;

        return view("guest.$this->view_folder.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'labor_demand_detail_id' => 'required|exists:labor_demand_details,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required',
            'religion' => 'nullable|string|max:60',
            'gender' => 'required',
            'marital_status' => 'required',
            'identity_card_number' => 'required',

            'file_type' => 'required|array',
            'file_path' => 'required|array',
            'file_type.*' => 'required|string|max:255',
            'file_path.*' => 'required|mimes:pdf,jpg,jpeg,png|max:4096',

            'emergency_contact_names' => 'required|array',
            'emergency_contact_relationships' => 'required|array',
            'emergency_contact_phones' => 'required|array',
            'emergency_contact_addresses' => 'required|array',

            'emergency_contact_names.*' => 'required|string|max:100',
            'emergency_contact_relationships.*' => 'required|string|max:100',
            'emergency_contact_phones.*' => 'required|string|max:100',
            'emergency_contact_addresses.*' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        $labor_demand_detail = LaborDemandDetail::findOrFail($request->labor_demand_detail_id);
        $branch_id = $labor_demand_detail->labor_demand->branch_id;
        $branch = \App\Models\Branch::find($branch_id);

        // * parent
        $model = new \App\Models\LaborApplication();

        $model->fill([
            'branch_id' => $branch_id,
            'labor_demand_detail_id' => $request->labor_demand_detail_id,
            'code' => generate_code(\App\Models\LaborApplication::class, 'code', 'date', 'LA', branch_sort: $branch->sort ?? null, date: date('Y-m-d')),
            'date' => date('Y-m-d'),
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'address_domicil' => $request->address_domicil,
            'phone' => $request->phone,
            'date_of_birth' => Carbon::parse($request->date_of_birth),
            'place_of_birth' => $request->place_of_birth,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'identity_card_number' => $request->identity_card_number,
        ]);

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: null,
                model: LaborApplication::class,
                model_id: $model->id,
                amount: 0,
                title: "Lamaran Pekerjaan",
                subtitle: $request->name . " mengajukan lamaran pekerjaan  " . $model->code,
                link: route('admin.labor-application.show', $model->id),
                update_status_link: route('admin.labor-application.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $token = md5(microtime());
            $generated_token = Str::substr($token, 0, 10);

            $qr_url = route('application', ['id' => $generated_token]);

            // * generate qr code
            $qr = QrCode::format('png')->size(250)->merge('/public/images/icon.png', .3)->generate($qr_url);
            $filename = 'labor-application-qr-code/' . $generated_token . '.png';
            Storage::disk('public')->put($filename, $qr);

            // * save token
            $application = new Application();
            $application->fill([
                'labor_application_id' => $model->id,
                'token' => $generated_token,
                'kode_akses' => mt_rand(100000, 999999),
                'qr' => $filename,
                'expiry' => Carbon::now()->addDays(3)->format('Y-m-d'),
            ]);
            $application->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }

        // * documents
        $document_file_data = [];
        foreach ($request->file_type as $key => $file_type) {
            $path = '';
            if (isset($request->file('file_path')[$key])) {
                $path = $this->upload_file($request->file('file_path')[$key], 'labor-application');
            }
            $document_file_data[] = [
                'type' => $file_type,
                'path' =>  $path,
            ];
        }

        try {
            $model->laborApplicationDocuments()->createMany($document_file_data);
        } catch (\Throwable $th) {
            DB::rollBack();

            // * delete uploaded file
            foreach ($document_file_data as $key => $value) {
                $this->delete_file($value['path'] ?? '');
            }

            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }

        // * emergency contact
        $emergency_contact_data = [];
        foreach ($request->emergency_contact_names as $key => $emergency_contact_name) {
            $emergency_contact_data[] = [
                'name' => $emergency_contact_name,
                'relationship' => $request->emergency_contact_relationships[$key],
                'phone' => $request->emergency_contact_phones[$key],
                'address' => $request->emergency_contact_addresses[$key],
            ];
        }

        try {
            $model->laborApplicationEmergencyContacts()->createMany($emergency_contact_data);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('guest.labor-application.success');
    }

    function success()
    {
        return view("guest.$this->view_folder.success");
    }
}
