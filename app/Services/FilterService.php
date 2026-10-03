<?php
namespace App\Services;
use Illuminate\Support\Facades\File;
class FilterService{
 public function __construct(private SparqlService $sparql){}
 public function all():array{
  $b=$this->sparql->bindings($this->sparql->query(File::get(resource_path('sparql/filters.rq'))));
  $out=[];foreach(['country','region','category','criterion'] as $k)$out[$k.'s']=collect($b)->pluck($k.'.value')->filter()->map(fn($v)=>preg_replace('/^.*[\/#]/','',$v))->unique()->sort()->values()->all();
  return $out;
 }
}
