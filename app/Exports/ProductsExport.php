<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Company;
use App\Models\Project;
use App\Models\Category;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private $categories;
    private $projects;
    private $companies;

    public function __construct()
    {
        $this->categories = Category::select('id', 'title')->get();
        $this->projects = Project::select('id', 'title')->get();
        $this->companies = Company::select('id', 'title')->get();
    }

    public function query()
    {
        return Product::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Наименование',
            'Kатегории',
            'Проекты',
            'Компании',
            'Артикул',
            'Цена оптовая',
            'Цена',
            'Количество',
            'Товар'
        ];
    }

    public function map($product): array
    {
        $category = $this->categories->where('id', $product->category_id)->first();
        $project = $this->projects->where('id', $product->project_id)->first();
        $company = $this->companies->where('id', $product->company_id)->first();

        return [
            $product->id,
            $product->title,
            $category->title ?? null,
            $project->title ?? null,
            $company->title ?? null,
            $product->id_code,
            $product->wholesale_price,
            $product->price,
            $product->count,
            ($product->type == 1) ? 'Новый' : 'Б/у'
        ];
    }
}