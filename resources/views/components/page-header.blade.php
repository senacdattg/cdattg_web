@props([
    'icon' => 'fa-home',
    'title' => 'Página',
    'subtitle' => 'Descripción de la página',
    'breadcrumb' => []
])

<section class="content-header dashboard-header py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                    style="width: 48px; height: 48px;">
                    <i class="fas {{ $icon }} text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
                    <p class="text-muted mb-0 font-weight-light">{{ $subtitle }}</p>
                </div>
            </div>
            <div class="col-sm-6">
                @if(!empty($breadcrumb))
                    <x-breadcrumb :items="$breadcrumb" />
                @endif
            </div>
        </div>
    </div>
</section>
