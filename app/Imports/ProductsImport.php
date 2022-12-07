<?php

namespace App\Imports;

use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Category;
use App\Models\Company;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{
    use SkipsFailures;

    private $user_id;
    private $company;
    private $categories;
    private $products;
    private $products_count;
    private $companies;

    public function __construct()
    {
        $this->user_id = auth()->user()->id;
        $this->first_store = auth()->user()->profile->company->stores->first();
        $this->categories = Category::select('id', 'slug', 'title')->get();
        $this->companies = Company::select('id', 'slug', 'title')->get();
        $this->products_count = Product::count();
    }

    public function rules()
    {
        return [
            'kategorii' => function($attribute, $value, $onFailure) {
                if (empty($value)) {
                    $onFailure('Неверно указана категория.'.$value);
                }
            }
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (is_null($row['naimenovanie']) || is_null($row['kategorii']) || is_null($row['kompanii'])) {
            return null;
        }

        $category = $this->categories->where('title', trim($row['kategorii']))->first();
        $company = $this->companies->where('title', trim($row['kompanii']))->first();

        if (is_null($company)) {

            $newCompany = new Company;
            $newCompany->sort_id = $this->companies->count() + 1;
            $newCompany->region_id = 0;
            $newCompany->slug = Str::slug($row['kompanii']);
            $newCompany->title = $row['kompanii'];
            $newCompany->image = 'no-image-mini.png';
            $newCompany->is_supplier = 1;
            $newCompany->is_customer = 0;
            $newCompany->status = 1;
            $newCompany->save();

            $company = $newCompany;

            $this->companies = Company::select('id', 'slug', 'title')->get();
        }

        if (is_null($category)) {

            $newCategory = new Category;
            $newCategory->sort_id = $this->categories->count() + 1;
            $newCategory->title = $row['kategorii'];
            $newCategory->slug = Str::slug($row['kategorii']);
            $newCategory->image = 'no-image-middle.png';
            $newCategory->saveAsRoot();
            $newCategory->lang = 'ru';
            $newCategory->status = 1;
            $newCategory->save();

            $category = $newCategory;

            $this->categories = Category::select('id', 'slug', 'title')->get();
        }

        return new Product([
            'sort_id' => ++$this->products_count,
            'user_id' => $this->user_id,
            'category_id' => $category->id,
            'company_id' => $company->id ?? 0,
            'project_id' => $_REQUEST['project_id'] ?? 0,
            'title' => $row['naimenovanie'],
            'slug' => Str::slug($row['naimenovanie']),
            'meta_title' => $row['naimenovanie'].' '.$row['artikul'],
            'meta_description' => $row['naimenovanie'].' - '.$category->title.' '.$row['artikul'],
            'barcodes' => (isset($row['shtrihkod'])) ? json_encode($row['shtrihkod']) : NULL,
            'id_code' => $row['artikul'] ?? NULL,
            'purchase_price' => (int) str_replace(" ", "", $row['cena_zakupocnaya']) ?? 0,
            'price' => (int) str_replace(" ", "", $row['cena']) ?? 0,
            'count_in_stores' => json_encode([$this->first_store->id => $row['kolicestvo'] ?? 0]),
            'count' => $row['kolicestvo'] ?? 0,
            'type' => ($row['tip'] == 'Товар') ? 1 : 2,
            'image' => 'no-image-middle.png',
            'lang' => 'ru',
            'status' => 1
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
