@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center custom-row">
            @forelse ($products as $product)
                <div class="col-md-4 mb-3">
                    <div class="card custom-card">
                        <img src="{{ $product->urlToImage }}" class="card-img-top" alt="{{ $product->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->title }}</h5>
                            <p class="card-text">{{ $product->description }}</p>
                            <p class="card-text"><small class="text-muted">Publicado por: {{ $product->author }} - {{ $product->publishedAt }}</small></p>
                        </div>
                    </div>
                </div>
            @empty
                <p>No hay artículos disponibles</p>
            @endforelse
        </div>
    </div>
@endsection
@section('paginate')
<div class="container d-flex justify-content-center">
    <ul class="pagination">
        <p class="d-inline">{{ $products->onEachSide(1)->links('vendor.pagination.custom') }}</p>
    </ul>
</div>
@endsection
