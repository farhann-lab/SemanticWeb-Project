<?php
namespace App\Http\Controllers;
use App\Services\FilterService;
class FilterController extends Controller{public function index(FilterService $s){return response()->json($s->all());}}
