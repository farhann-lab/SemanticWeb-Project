<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SearchRequest extends FormRequest{
 public function authorize():bool{return true;}
 public function rules():array{return[
  'q'=>['nullable','string','max:100'],'country'=>['nullable','string','max:100'],'region'=>['nullable','string','max:100'],
  'category'=>['nullable','string','max:100'],'criterion'=>['nullable','string','max:20'],
  'year_min'=>['nullable','integer','min:1','max:2100'],'year_max'=>['nullable','integer','min:1','max:2100'],
  'danger'=>['nullable','in:true,false'],'transboundary'=>['nullable','in:true,false'],
  'sort'=>['nullable','in:name_asc,name_desc,year_asc,year_desc'],'page'=>['nullable','integer','min:1'],'limit'=>['nullable','integer','min:1','max:100']
 ];}
}
