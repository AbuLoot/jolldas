<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Models\Region;
use App\Models\Company;
use App\Models\Currency;

use Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Company::class);

        $companies = Company::orderBy('sort_id')->paginate(50);

        return view('joystick.companies.index', compact('companies'));
    }

    public function actionCompanies(Request $request)
    {
        $this->validate($request, [
            'companies_id' => 'required'
        ]);

        if (in_array($request->action, ['0', '1', '2', '3'])) {
            Company::whereIn('id', $request->companies_id)->update(['status' => $request->action]);
        }
        elseif($request->action == 'destroy') {

            foreach($request->companies_id as $company_id) {
                $company = company::find($company_id);
                $this->authorize('delete', $company);

                if (file_exists('img/companies/'.$company->image) && $company->image != 'no-image-mini.png') {
                    Storage::delete('img/companies/'.$company->image);
                }

                $company->delete();
            }
        }

        return response()->json(['status' => true]);
    }

    public function create($lang)
    {
        $this->authorize('create', Company::class);

        $regions = Region::orderBy('sort_id')->get()->toTree();
        $currencies = Currency::get();

        return view('joystick.companies.create', compact('regions', 'currencies'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Company::class);

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:companies',
        ]);

        $company = new Company;

        if ($request->hasFile('image')) {

            $logoName = Str::slug($request->title).'.'.$request->image->getClientOriginalExtension();
            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = ($request->sort_id) ? $request->sort_id : $company->count() + 1;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->currency_id = $request->currency_id;
        $company->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $company->title = $request->title;
        $company->bin = $request->bin;
        $company->image = (isset($logoName)) ? $logoName : 'no-image-mini.png';
        $company->about = $request->about;
        $company->phones = $request->phones;
        $company->links = $request->links;
        $company->emails = $request->emails;
        $company->legal_address = $request->legal_address;
        $company->actual_address = $request->actual_address;
        $company->is_supplier = ($request->is_supplier == 'on') ? 1 : 0;
        $company->is_customer = ($request->is_customer == 'on') ? 1 : 0;
        $company->status = ($request->status == 'on') ? 1 : 0;
        $company->save();

        return redirect($request->lang.'/admin/companies')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $company = Company::findOrFail($id);
        $currencies = Currency::get();

        $this->authorize('update', $company);


        return view('joystick.companies.edit', compact('regions', 'company', 'currencies'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $company = Company::findOrFail($id);

        $this->authorize('update', $company);

        if ($request->hasFile('image')) {

            if (file_exists($company->image)) {
                Storage::delete($company->image);
            }

            $logoName = Str::slug($request->title).'.'.$request->image->getClientOriginalExtension();
            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = ($request->sort_id > 0) ? $request->sort_id : $company->count() + 1;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->currency_id = $request->currency_id;
        $company->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $company->title = $request->title;
        $company->bin = $request->bin;
        if (isset($logoName)) $company->image = $logoName;
        $company->about = $request->about;
        $company->phones = $request->phones;
        $company->links = $request->links;
        $company->emails = $request->emails;
        $company->legal_address = $request->legal_address;
        $company->actual_address = $request->actual_address;
        $company->is_supplier = ($request->is_supplier == 'on') ? 1 : 0;
        $company->is_customer = ($request->is_customer == 'on') ? 1 : 0;
        $company->status = ($request->status == 'on') ? 1 : 0;
        $company->save();

        return redirect($lang.'/admin/companies')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $company = Company::find($id);

        $this->authorize('delete', $company);

        if (file_exists('img/companies/'.$company->image) && $company->image != 'no-image-mini.png') {
            Storage::delete('img/companies/'.$company->image);
        }

        $company->delete();

        return redirect($lang.'/admin/companies')->with('status', 'Запись удалена.');
    }
}