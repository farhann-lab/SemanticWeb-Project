{{-- resources/views/components/explore/Pagination.blade.php --}}
@props([
    'result' => [],
])

<x-explore.ExplorePagination :result="$result" />
