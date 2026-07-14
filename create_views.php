<?php

$pages = [
    'countries' => 'Countries Directory',
    'weather' => 'Global Weather',
    'ports' => 'Port Locations',
    'news' => 'News Intelligence',
    'settings' => 'Settings & Admin'
];

foreach ($pages as $slug => $title) {
    $content = <<<BLADE
@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="custom-card text-center py-5">
            <h3 class="fw-bold mb-3">$title</h3>
            <p class="text-muted">Fitur ini dapat dikembangkan lebih lanjut sesuai dengan kebutuhan $title pada spesifikasi.</p>
        </div>
    </div>
</div>
@endsection
BLADE;
    file_put_contents(__DIR__ . "/resources/views/dashboard/{$slug}.blade.php", $content);
}
echo "Views created.";
