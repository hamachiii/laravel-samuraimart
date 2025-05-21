@extends('layouts.app')

@section('content')
<div class="container  d-flex justify-content-center mt-3">
   <div class="w-75">
       <h1>お気に入り</h1>

       <hr>

       <div class="row">
           @foreach ($favorite_products as $favorite_product)
               <div class="col-md-7 mt-2">
                   <div class="d-inline-flex">
                       <a href="{{ route('products.show', $favorite_product->id) }}" class="w-25">
                           <img src="{{ asset('img/dummy.png')}}" class="img-fluid w-100">
                       </a>
                       <div class="container mt-3">
                           <h5 class="w-100 samuraimart-favorite-item-text">{{ $favorite_product->name }}</h5>
                           <h6 class="w-100 samuraimart-favorite-item-text">&yen;{{ $favorite_product->price }}</h6>
                       </div>
                   </div>
               </div>
               <div class="col-md-2 d-flex align-items-center justify-content-end">
                   <a href="{{ route('favorites.destroy', $favorite_product->id) }}" class="samuraimart-favorite-item-delete" onclick="event.preventDefault(); document.getElementById('favorites-destroy-form{{$favorite_product->id}}').submit();">
                       削除
                   </a>
                   <form id="favorites-destroy-form{{$favorite_product->id}}" action="{{ route('favorites.destroy', $favorite_product->id) }}" method="POST" class="d-none">
                       @csrf
                       @method('DELETE')
                   </form>
               </div>
               <div class="col-md-3 d-flex align-items-center justify-content-end">
                   <button type="submit" class="btn samuraimart-favorite-add-cart">カートに入れる</button>
               </div>
           @endforeach
       </div>

       <hr>
   </div>
</div>
@endsection