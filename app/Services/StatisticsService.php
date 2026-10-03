<?php
namespace App\Services;
use Illuminate\Support\Facades\File;
class StatisticsService{
 public function __construct(private SparqlService $sparql){}
 public function get():array{
  return collect($this->sparql->bindings($this->sparql->query(File::get(resource_path('sparql/statistics.rq')))))->map(fn($b)=>[
   'category'=>preg_replace('/^.*[\/#]/','',$b['category']['value']??''),'total'=>(int)($b['total']['value']??0)
  ])->values()->all();
 }
}
