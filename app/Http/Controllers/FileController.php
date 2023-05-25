<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use App\Models\File;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Customers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            if (Auth::user()->can('customer_view')) {
                $files = File::with('customer');
            } else {
                $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
                $customerId = Auth::user()->id;
                if (isset($customerUser)) {
                  $customerId = $customerUser->customer_id;
                }
                $files = File::with('customer')->where('customer_id', $customerId);
            }
            if ($request->customer_id != '') {
                $files->where('customer_id', $request->customer_id);
            }
            if ($request->file_id != '') {
                $files->where('id', $request->file_id);
            }
            return Datatables::of($files)
            ->addIndexColumn()
            ->addColumn('customer_name', function ($row) {
              return ucwords($row->customer->customer_name);
            })
            ->addColumn('file_name', function ($row) {
              return ucwords($row->name);
            })
            ->addColumn('action', function($row)
            {
                // $btn = '<div class="dropdown">
                //       <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                //           . . .
                //       </button>
                //       <div class="dropdown-menu">';
                // if (Auth::user()->can('user_view'))
                // {
                $btn = '<a class="dropdown-item w-50" href="/download_file/'. $row->id .'" style="text-decoration: none">
                            <i data-feather="download"></i> Download
                        </a>';
                // }
                // $btn .= '</div>
                // </div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        if (Auth::user()->can('customer_view')) {
            $customers = Customers::get();
            $fileNames = File::groupBy('file_name')->get();
        } else {
            $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
            $customerId = Auth::user()->id;
            if (isset($customerUser)) {
              $customerId = $customerUser->customer_id;
            }
            $customers = Customers::where('id', $customerId)->get();
            $fileNames = File::groupBy('file_name')->where('customer_id', $customerId)->get();
        }
        return view('admin.stored_files.index', compact('customers', 'fileNames'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'customer_id' => 'required',
        ]);
        $name = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->store('public/files');
        $file = File::create([
            'name' => Carbon::now()->format('d_m_Y_g_i_s').'_'.$name,
            'path' => $path,
            'customer_id' => $request->customer_id,
            'file_name' => $name
        ]);
        return redirect()->back()->with('File has been uploaded Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }

    public function downloadFile(Request $request, $id)
    {
        $filePath = File::where('id', $id)->first();
        $download = Storage::download($filePath->path, $filePath->name);
        return $download;
    }

    public function truncateFiles()
    {
        File::truncate();
        return redirect()->back();
    }

    public function getCustomerFiles(Request $request)
    {
        $files = File::query();
        if (!empty($request->customer)) {
            $files = $files->where('customer_id', $request->customer);
        }
        $files = $files->get();
        return response()->json(['status' => true, 'files' => $files]);
    }
}
